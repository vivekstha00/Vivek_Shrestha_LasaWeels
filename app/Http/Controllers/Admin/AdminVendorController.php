<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Http\Request;

class AdminVendorController extends Controller
{
    public function index()
    {
        $vendors = VendorProfile::with('user')->latest()->get();
        return view('admin.pages.manage-vendor', compact('vendors'));
    }

    public function show($id)
    {
        $profile = VendorProfile::with('user')->findOrFail($id);
        $docs = Document::where('user_id', $profile->user_id)->latest()->get();

        return view('admin.pages.vendor-details', compact('profile', 'docs'));
    }

    public function approve($id)
    {
        $profile = VendorProfile::findOrFail($id);

        $profile->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'remarks'     => null,
        ]);

        User::where('id', $profile->user_id)->update(['status' => 'approved']);

        // Optional later: send email notification

        return back()->with('success', 'Vendor approved.');
    }

    public function reject(Request $request, $id)
    {
        $profile = VendorProfile::findOrFail($id);

        $profile->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'remarks'     => $request->input('remarks'),
        ]);

        User::where('id', $profile->user_id)->update(['status' => 'rejected']);

        return back()->with('success', 'Vendor rejected.');
    }

    public function resubmit(Request $request, $id)
    {
        $profile = VendorProfile::findOrFail($id);

        $profile->update([
            'status'      => 'resubmit',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'remarks'     => $request->input('remarks'),
        ]);

        User::where('id', $profile->user_id)->update(['status' => 'pending']);

        return back()->with('success', 'Marked as resubmit requested.');
    }
}
