<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorProfile;
use App\Models\Vehicle;
use App\Models\Document;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $statistics = [
            'totalUsers' => User::where('role','user')->count(),
            'totalVendors' => VendorProfile::count(),
            'totalVehicles' => Vehicle::count(),

            'pendingVendors' => VendorProfile::where('status','pending')->count(),
            'pendingVehicles' => Vehicle::where('status','pending')->count(),
            'pendingDocs' => Document::where('status','pending')->count(),

            'recentUsers' => User::where('role','user')->latest()->take(5)->get(),
        ];

        return view('admin.pages.dashboard', compact('statistics'));
    }
}
