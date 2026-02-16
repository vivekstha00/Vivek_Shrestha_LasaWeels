<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');

        $users = User::where('role','user')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name','like',"%{$q}%")
                    ->orWhere('email','like',"%{$q}%");
                });
            })
            ->when($status, fn($query) => $query->where('status',$status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'total'     => User::where('role','user')->count(),
            'active'    => User::where('role','user')->where('status','approved')->count(),
            'pending'   => User::where('role','user')->where('status','pending')->count(),
            'suspended' => User::where('role','user')->where('status','suspended')->count(),
        ];

        return view('admin.pages.manage-user', compact('users','counts'));
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
