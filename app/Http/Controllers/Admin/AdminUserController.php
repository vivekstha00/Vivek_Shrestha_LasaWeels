<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.pages.manage-user', compact('users'));
    }

    public function approve($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        $user->update([
            'status' => 'approved',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_note' => null,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        $user->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_note' => $request->note,
        ]);


        return redirect()->route('admin.users.index')->with('success', 'User rejected successfully.');
    }
}
