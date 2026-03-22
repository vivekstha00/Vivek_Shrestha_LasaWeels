<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index()
    {
        $activeOffers = DiscountCode::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $featuredVehicles = Vehicle::with(['primaryImage'])
            ->where('status', 'approved')
            ->where('is_active', 1)
            ->latest()
            ->take(6)
            ->get();

        return view('user.pages.home', compact('activeOffers', 'featuredVehicles'));
    }
}
