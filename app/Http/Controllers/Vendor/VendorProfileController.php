<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorProfileController extends Controller
{

    public function edit()
    {
        $vendor = Auth::user();
        return view('vendor.pages.profile', compact('vendor'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $vendor */
        $vendor = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $vendor->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
