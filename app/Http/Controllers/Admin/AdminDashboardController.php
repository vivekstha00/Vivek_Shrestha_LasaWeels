<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorProfile;
use App\Models\Vehicle;
use App\Models\Document;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\Booking;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $statistics = [
            'totalUsers' => User::where('role', 'user')->count(),
            'totalVendors' => VendorProfile::count(),
            'totalVehicles' => Vehicle::count(),

            'pendingVendors' => VendorProfile::where('status', 'pending')->count(),
            'pendingVehicles' => Vehicle::where('status', 'pending')->count(),
            'pendingDocs' => Document::where('status', 'pending')->count(),

            'recentUsers' => User::where('role', 'user')->latest()->take(5)->get(),

            // Loyalty analytics
            'loyaltyAccounts' => LoyaltyAccount::count(),
            'availablePoints' => LoyaltyAccount::sum('available_points'),
            'earnedPoints' => LoyaltyAccount::sum('lifetime_earned_points'),
            'redeemedPoints' => LoyaltyAccount::sum('lifetime_redeemed_points'),
            'totalLoyaltyDiscount' => Booking::sum('loyalty_discount_amount'),

            'topLoyaltyUsers' => LoyaltyAccount::with('user')
                ->orderByDesc('available_points')
                ->take(5)
                ->get(),

            'recentLoyaltyTransactions' => LoyaltyTransaction::with(['user', 'booking'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('admin.pages.dashboard', compact('statistics'));
    }
}
