<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class VendorDashboardController extends Controller
{
public function index() {
    $vendorId = Auth::id();
    return view('vendor.pages.dashboard', [
        'totalVehicles' => Vehicle::where('vendor_id',$vendorId)->count(),
        'pendingVehicles' => Vehicle::where('vendor_id',$vendorId)->where('status','pending')->count(),
        'recentVehicles' => Vehicle::where('vendor_id',$vendorId)->latest()->take(5)->get(),
        'activeBookings' => 0,
        'monthlyRevenue' => '—',
    ]);
    }
}
