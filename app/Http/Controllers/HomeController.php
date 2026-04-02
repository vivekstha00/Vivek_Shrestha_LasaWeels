<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\DiscountCode;
use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index()
    {
        $activeOffers = DiscountCode::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->get();

        $featuredVehicles = Vehicle::with(['primaryImage'])
            ->where('status', 'approved')
            ->where('is_active', 1)
            ->latest()
            ->take(6)
            ->get();

        $latestBlogs = BlogPost::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('user.pages.home', compact('activeOffers', 'featuredVehicles', 'latestBlogs'));
    }
}
