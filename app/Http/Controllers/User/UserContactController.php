<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactRequest;
use Illuminate\Support\Facades\Auth;

class UserContactController extends Controller
{
    public function create()
    {
        return view('user.pages.contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|max:255',
            'message' => 'required'
        ]);

        ContactRequest::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->back()
            ->with('success', 'Your query has been submitted.');
    }
}
