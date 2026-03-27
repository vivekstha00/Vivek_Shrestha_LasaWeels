<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\VendorApprovedNotification;
use App\Notifications\VendorRejectedNotification;

class AdminVendorController extends Controller
{
    public function index()
    {
        $vendors = VendorProfile::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.vendors.index', compact('vendors'));
    }

    public function show($id)
    {
        $profile = VendorProfile::with('user')->findOrFail($id);
        $docs = Document::where('user_id', $profile->user_id)->latest()->get();

        return view('admin.vendors.show', compact('profile', 'docs'));
    }

    public function approve($id)
    {
        $profile = VendorProfile::findOrFail($id);

        $profile->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'remarks'     => null,
        ]);

        User::where('id', $profile->user_id)->update([
            'vendor_status' => 'approved',
            'verification_note' => null,
        ]);

        Document::where('user_id', $profile->user_id)
            ->update(['status' => 'approved']);

        $vendorUser = User::find($profile->user_id);
        if ($vendorUser) {
            $vendorUser->notify(new VendorApprovedNotification());
        }

        return back()->with('success', 'Vendor approved.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        $profile = VendorProfile::findOrFail($id);

        $profile->update([
            'status'      => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        User::where('id', $profile->user_id)->update([
            'vendor_status' => 'rejected',
            'verification_note' => $request->remarks,
        ]);

        Document::where('user_id', $profile->user_id)
            ->update(['status' => 'rejected']);

        $vendorUser = User::find($profile->user_id);
        if ($vendorUser) {
            $vendorUser->notify(new VendorRejectedNotification($request->remarks));
        }

        return back()->with('success', 'Vendor rejected.');
    }

    public function resubmit(Request $request, $id)
    {
        $profile = VendorProfile::findOrFail($id);

        $profile->update([
            'status'      => 'resubmit',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'remarks'     => $request->input('remarks'),
        ]);

        User::where('id', $profile->user_id)->update([
            'vendor_status' => 'resubmit',
            'verification_note' => $request->input('remarks'),
        ]);

        return back()->with('success', 'Marked as resubmit requested.');
    }

    public function delete($id)
    {
        $vendor = VendorProfile::findOrFail($id);

        $vendor->delete();

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
