<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Services\VendorSubscriptionService;
use Illuminate\Support\Facades\Auth;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();

        $statistics = [
            'totalVehicles' => Vehicle::where('vendor_id', $vendorId)->count(),
            'activeVehicles' => Vehicle::where('vendor_id', $vendorId)
                ->where('is_active', 1)
                ->count(),

            'totalDrivers' => Driver::where('vendor_id', $vendorId)->count(),
            'approvedDrivers' => Driver::where('vendor_id', $vendorId)
                ->where('status', 'approved')
                ->count(),

            'totalBookings' => Booking::whereHas('vehicle', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),

            'pendingBookings' => Booking::whereHas('vehicle', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('status', 'pending')->count(),

            'confirmedBookings' => Booking::whereHas('vehicle', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('status', 'confirmed')->count(),

            'completedBookings' => Booking::whereHas('vehicle', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('status', 'completed')->count(),

            'totalRevenue' => Payment::where('vendor_id', $vendorId)
                ->where('status', 'completed')
                ->sum('vendor_amount'),
        ];

        $recentBookings = Booking::with(['user', 'vehicle'])
            ->whereHas('vehicle', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->latest()
            ->take(5)
            ->get();

        $availableDrivers = Driver::where('vendor_id', $vendorId)
            ->where('status', 'approved')
            ->where('availability_status', 'available')
            ->latest()
            ->take(5)
            ->get();

        $recentCustomers = Booking::with('user')
            ->whereHas('vehicle', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->latest()
            ->take(20)
            ->get()
            ->pluck('user')
            ->filter(fn ($user) => $user && $user->role === 'user')
            ->unique('id')
            ->values()
            ->take(5);

        $subscriptionSummary = app(VendorSubscriptionService::class)->getSummary($vendorId);

        return view('vendor.pages.dashboard', compact(
            'statistics',
            'recentBookings',
            'availableDrivers',
            'recentCustomers',
            'subscriptionSummary'
        ));
    }
}
