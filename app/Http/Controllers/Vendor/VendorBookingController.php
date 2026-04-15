<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class VendorBookingController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();

        $vendorScopedBookings = Booking::query()
            ->whereHas('vehicle', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            });

        $now = now();

        $bookings = Booking::with('vehicle', 'user')
            ->whereHas('vehicle', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->orderByDesc('pickup_datetime')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $upcomingBookings = (clone $vendorScopedBookings)
            ->with(['vehicle:id,title,brand,model', 'user:id,name'])
            ->whereIn('status', ['pending', 'confirmed', 'approved'])
            ->where('pickup_datetime', '>=', $now)
            ->where('pickup_datetime', '<=', $now->copy()->addDay())
            ->orderBy('pickup_datetime')
            ->limit(6)
            ->get();

        $currentTrips = (clone $vendorScopedBookings)
            ->with(['vehicle:id,title,brand,model', 'user:id,name'])
            ->whereIn('status', ['confirmed', 'approved', 'active'])
            ->where('pickup_datetime', '<=', $now)
            ->where('drop_datetime', '>=', $now)
            ->orderBy('drop_datetime')
            ->limit(6)
            ->get();

        $missedArrivalBookings = (clone $vendorScopedBookings)
            ->with(['vehicle:id,title,brand,model', 'user:id,name'])
            ->whereIn('status', ['pending', 'confirmed', 'approved'])
            ->where('pickup_datetime', '<', $now->copy()->subMinutes(30))
            ->orderByDesc('pickup_datetime')
            ->limit(6)
            ->get();

        $bookingMonitorSummary = [
            'upcoming' => $upcomingBookings->count(),
            'on_trip' => $currentTrips->count(),
            'missed_arrival' => $missedArrivalBookings->count(),
        ];

        return view('vendor.pages.bookings.index', compact(
            'bookings',
            'upcomingBookings',
            'currentTrips',
            'missedArrivalBookings',
            'bookingMonitorSummary'
        ));
    }

    public function show(Booking $booking)
    {
        // Security: ensure booking belongs to vendor
        if ($booking->vehicle->vendor_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['vehicle.primaryImage', 'vehicle.images', 'user', 'payment']);

        return view('vendor.pages.bookings.show', compact('booking'));
    }
}
