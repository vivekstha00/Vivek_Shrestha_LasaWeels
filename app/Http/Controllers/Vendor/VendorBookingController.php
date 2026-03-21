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

        $bookings = Booking::with('vehicle', 'user')
            ->whereHas('vehicle', function ($query) {
                $query->where('vendor_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('vendor.pages.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Security: ensure booking belongs to vendor
        if ($booking->vehicle->vendor_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['vehicle', 'user', 'payment']);

        return view('vendor.pages.bookings.show', compact('booking'));
    }
}
