<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;

        $query->whereHas('user', function ($q) use ($search) {
            $q->where('role', 'user')
              ->where(function ($sub) use ($search) {
                  $sub->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
              });
            });
        }

        $documents = $query->paginate(10)->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    public function show(Document $document)
    {
        $document->load('user');

        abort_unless(
            $document->user &&
            $document->user->role === 'user' &&
            in_array($document->type, ['license', 'citizenship']),
            404
        );

        return view('admin.documents.show', compact('document'));
    }

    public function approve(Document $document)
    {
        $document->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'remarks'     => null,
        ]);

        return back()->with('success', ucfirst($document->type) . ' approved successfully.');
    }

    public function reject(Request $request, Document $document)
    {
        $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        $document->update([
            'status'      => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        return back()->with('success', ucfirst($document->type) . ' rejected successfully.');
    }
}
