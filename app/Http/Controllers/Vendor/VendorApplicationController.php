<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\VendorRequestSubmittedNotification;
use Illuminate\Support\Facades\Notification;

class VendorApplicationController extends Controller
{
    public function create()
    {
        return view('user.pages.corporate-rent'); // your navbar page
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name'    => ['required', 'string', 'max:255'],
            'contact_person'  => ['required', 'string', 'max:255'],
            'phone'           => ['required', 'string', 'max:15', 'unique:users,phone'],
            'address'         => ['nullable', 'string', 'max:255'],

            // vendor login credentials
            'email'           => ['required', 'email', 'unique:users,email'],
            'password'        => ['required', 'min:6', 'confirmed'],

            // documents (basic set)
            'documents'       => ['required'],
            'documents.*'     => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        // Create vendor user (blocked until approved)
        $user = User::create([
            'name'     => $data['contact_person'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => 'vendor',
            'status'   => 'approved',
            'vendor_status' => 'pending',
        ]);

        VendorProfile::create([
            'user_id'        => $user->id,
            'company_name'   => $data['company_name'],
            'contact_person' => $data['contact_person'],
            'phone'          => $data['phone'],
            'address'        => $data['address'] ?? null,
            'status'         => 'pending',
        ]);

        // Store vendor documents in documents table
        foreach ($request->file('documents', []) as $file) {
            $path = $file->store('vendor_documents', 'public');

            Document::create([
                'user_id'    => $user->id,
                'type'       => 'vendor_business_doc',
                'file_path'  => $path,
                'status'     => 'pending',
            ]);
        }

        //  Notify admin: vendor request submitted
        Notification::route('mail', config('app.admin_email'))
            ->notify(new VendorRequestSubmittedNotification($user));


        return redirect()->route('login')
            ->with('success', 'Vendor request submitted. Wait for admin approval.');
    }
    public function verification()
    {
        $vendor = Auth::user();
        return view('vendor.pages.verification', compact('vendor'));
    }

    public function resubmit(Request $request)
    {
        $vendor = Auth::user();

        $request->validate([
            'document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $path = $request->file('document')->store('vendor_documents', 'public');

        Document::create([
            'user_id' => $vendor->id,
            'type' => 'vendor_business_doc',
            'file_path' => $path,
            'status' => 'pending',
        ]);

        User::where('id', $vendor->id)->update([
            'vendor_status' => 'pending',
            'verification_note' => null,
        ]);

        VendorProfile::where('user_id', $vendor->id)
            ->update(['status' => 'pending']);

        return back()->with('success', 'Document resubmitted successfully.');
    }
}
