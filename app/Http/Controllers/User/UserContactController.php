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
        $vendors = \App\Models\User::with('vendorProfile')
            ->where('role', 'vendor')
            ->where('vendor_status', 'approved')
            ->whereHas('vendorProfile', function ($query) {
                $query->where('status', 'approved');
            })
            ->latest()
            ->get();

        return view('user.pages.contact', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'subject' => 'required|max:255',
            'message' => 'required'
        ]);

        ContactRequest::create([
            'user_id' => Auth::id(),
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->back()
            ->with('success', 'Your query has been submitted.');
    }
}
