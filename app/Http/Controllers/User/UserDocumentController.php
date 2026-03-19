<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserDocumentController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'type'            => ['required', 'string', 'max:50'],
            'file'            => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'issued_at'       => ['nullable', 'date'],
            'expires_at'      => ['nullable', 'date', 'after_or_equal:issued_at'],
        ]);


        $path = $request->file('file')->store("documents/{$user->id}", 'public');

        $existing = Document::where('user_id', $user->id)
            ->where('type', $validated['type'])
            ->first();

        if ($existing && $existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
            Storage::disk('public')->delete($existing->file_path);
        }

        Document::updateOrCreate(
            [
                'user_id' => $user->id,
                'type'    => $validated['type'],
            ],
            [
                'file_path'       => $path,
                'document_number' => $validated['document_number'] ?? null,
                'issued_at'       => $validated['issued_at'] ?? null,
                'expires_at'      => $validated['expires_at'] ?? null,

                'status'      => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'remarks'     => null,
            ]
        );

        return back()->with('success', 'Document uploaded and sent for verification.');
    }

    public function update(Request $request, Document $document)
    {
        $user = Auth::user();

        abort_unless($document->user_id === $user->id, 403);

        $validated = $request->validate([
            'file'            => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'issued_at'       => ['nullable', 'date'],
            'expires_at'      => ['nullable', 'date', 'after_or_equal:issued_at'],
        ]);

        $needsReverification = false;

        if (($validated['document_number'] ?? null) != $document->document_number) {
            $needsReverification = true;
        }

        if (($validated['issued_at'] ?? null) != $document->issued_at) {
            $needsReverification = true;
        }

        if (($validated['expires_at'] ?? null) != $document->expires_at) {
            $needsReverification = true;
        }

        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $validated['file_path'] = $request->file('file')
                ->store("documents/{$user->id}", 'public');

            $needsReverification = true;
        }

        if ($needsReverification) {
            $validated['status'] = 'pending';
            $validated['reviewed_by'] = null;
            $validated['reviewed_at'] = null;
            $validated['remarks'] = null;
        }

        $document->update($validated);

        return back()->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        $user = Auth::user();

        abort_unless($document->user_id === $user->id, 403);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted.');
    }
}
