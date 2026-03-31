<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VendorUserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $users = User::query()
            ->where('role', 'user')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->withCount('bookings')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('vendor.pages.users.index', compact('users', 'q'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'user', 404);

        // placeholders until booking/doc is implemented
        $totalBookings = 0;

        // If you have Document model for users too, we can compute real status later.
        $docStatus = 'Not Verified'; 

        return view('vendor.pages.users.show', compact('user', 'totalBookings', 'docStatus'));
    }
}
