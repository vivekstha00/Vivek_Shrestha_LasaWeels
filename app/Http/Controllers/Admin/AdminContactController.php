<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactRequest;
use Illuminate\Support\Facades\Mail;


class AdminContactController extends Controller
{
    public function index()
    {
        $contacts = ContactRequest::latest()->paginate(10);
        return view('admin.contacts.index', compact('contacts'));
    }

    public function show($id)
    {
        $contact = ContactRequest::findOrFail($id);
        return view('admin.contacts.show', compact('contact'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required'
        ]);

        $contact = ContactRequest::findOrFail($id);

        $contact->update([
            'reply_message' => $request->reply_message,
            'status' => 'replied',
            'replied_at' => now(),
        ]);

        $replyToEmail = $contact->user->email ?? $contact->email;

        if ($replyToEmail) {
            Mail::raw($request->reply_message, function ($message) use ($replyToEmail, $contact) {
                $message->to($replyToEmail)
                    ->subject('Reply: ' . $contact->subject);
            });
        }

        return back()->with('success', 'Reply saved successfully.');
    }
    public function destroy($id)
    {
        $contact = ContactRequest::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }
}
