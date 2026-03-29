<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserContactController extends Controller
{
    public function create()
    {
        $vendors = User::with('vendorProfile')
            ->where('role', 'vendor')
            ->where('vendor_status', 'approved')
            ->whereHas('vendorProfile', function ($query) {
                $query->where('status', 'approved');
            })
            ->latest()
            ->get();

        $office = [
            'name' => 'LasaWheels Head Office',
            'phone' => '+977-9800000000',
            'email' => 'support@lasawheels.com',
            'address' => 'Pokhara-8, Kaski, Nepal',
            'latitude' => 28.2096,
            'longitude' => 83.9856,
        ];

        return view('user.pages.contact', compact('vendors', 'office'));
    }

    public function showVendor(User $vendor)
    {
        $vendor->load('vendorProfile');

        $isApprovedVendor = $vendor->role === 'vendor'
            && $vendor->vendor_status === 'approved'
            && optional($vendor->vendorProfile)->status === 'approved';

        abort_unless($isApprovedVendor, 404);

        return view('user.pages.contact-vendor-show', compact('vendor'));
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
