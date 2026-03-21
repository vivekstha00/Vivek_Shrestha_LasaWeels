<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminLoyaltyController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $tier = $request->query('tier');

        $accounts = LoyaltyAccount::with('user')
            ->whereHas('user', function ($query) use ($q) {
                $query->where('role', 'user');

                if ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
                }
            })
            ->when($tier, fn ($query) => $query->where('tier', $tier))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'accounts' => LoyaltyAccount::count(),
            'available_points' => LoyaltyAccount::sum('available_points'),
            'earned_points' => LoyaltyAccount::sum('lifetime_earned_points'),
            'redeemed_points' => LoyaltyAccount::sum('lifetime_redeemed_points'),
        ];

        return view('admin.loyalty.index', compact('accounts', 'counts'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'user', 404);

        $user->load('loyaltyAccount');

        $transactions = LoyaltyTransaction::with('booking')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('admin.loyalty.show', compact('user', 'transactions'));
    }
}
