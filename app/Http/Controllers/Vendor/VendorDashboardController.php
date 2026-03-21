<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Payment;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();

        $totalVehicles = Vehicle::where('vendor_id', $vendorId)->count();
        $pendingVehicles = Vehicle::where('vendor_id', $vendorId)->where('status', 'pending')->count();
        $recentVehicles = Vehicle::where('vendor_id', $vendorId)->latest()->take(5)->get();

        $activeBookings = Booking::whereHas('vehicle', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
        ->whereIn('status', ['confirmed', 'active'])
        ->count();

        $monthlyRevenueValue = Payment::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('vendor_amount');

        $monthlyRevenue = 'Rs. ' . number_format($monthlyRevenueValue, 2);

        $totalCustomerPaid = Payment::where('vendor_id', $vendorId)->sum('amount');
        $totalCommission = Payment::where('vendor_id', $vendorId)->sum('platform_commission');
        $totalNet = Payment::where('vendor_id', $vendorId)->sum('vendor_amount');

        $totalLoyaltyDiscount = Payment::where('vendor_id', $vendorId)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->sum('bookings.loyalty_discount_amount');

        $discountedBookingsCount = Booking::whereHas('vehicle', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
        ->where('loyalty_discount_amount', '>', 0)
        ->count();

        $originalBookingValue = $totalCustomerPaid + $totalLoyaltyDiscount;

        return view('vendor.pages.dashboard', compact(
            'totalVehicles',
            'pendingVehicles',
            'recentVehicles',
            'activeBookings',
            'monthlyRevenue',
            'totalCustomerPaid',
            'totalCommission',
            'totalNet',
            'totalLoyaltyDiscount',
            'discountedBookingsCount',
            'originalBookingValue'
        ));
    }
}
