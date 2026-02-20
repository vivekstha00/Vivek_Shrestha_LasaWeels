<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBookingController extends Controller
{
    // 1) SEARCH available vehicles (Find Vehicle button)
    public function search(Request $request)
    {
        $data = $request->validate([
            'service'          => ['required', 'in:self,driver'],
            'pickup_location'  => ['required', 'string', 'max:255'],
            'drop_location'    => ['required', 'string', 'max:255'],
            'pickup_datetime'  => ['required', 'date'],
            'drop_datetime'    => ['required', 'date', 'after:pickup_datetime'],
        ]);

        $pickup = Carbon::parse($data['pickup_datetime']);
        $drop   = Carbon::parse($data['drop_datetime']);

        $vehicles = Vehicle::query()
            ->where('status', 'available')
            ->whereDoesntHave('bookings', function ($q) use ($pickup, $drop) {
                $q->whereIn('status', ['pending', 'confirmed'])
                  ->where('pickup_datetime', '<=', $drop)
                  ->where('drop_datetime', '>=', $pickup);
            })
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        return view('user.pages.search-results', [
            'vehicles' => $vehicles,
            'search'   => $data, // keep user selections
        ]);
    }

    // 2) BOOKING CHECKOUT (after clicking "Book Now" from details)
    public function create(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'service'          => ['required', 'in:self,driver'],
            'pickup_location'  => ['required', 'string', 'max:255'],
            'drop_location'    => ['required', 'string', 'max:255'],
            'pickup_datetime'  => ['required', 'date'],
            'drop_datetime'    => ['required', 'date', 'after:pickup_datetime'],
        ]);

        if ($vehicle->status !== 'available') {
            return back()->withErrors(['vehicle' => 'Vehicle is not available.']);
        }

        $pickup = Carbon::parse($data['pickup_datetime']);
        $drop   = Carbon::parse($data['drop_datetime']);

        // Overlap check again (important)
        $overlap = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('pickup_datetime', '<=', $drop)
            ->where('drop_datetime', '>=', $pickup)
            ->exists();

        if ($overlap) {
            return back()->withErrors(['dates' => 'Vehicle already booked for selected time.']);
        }

        // price calculation
        $hours = $pickup->diffInHours($drop);
        $days  = max(1, (int) ceil($hours / 24));

        $service = $data['service'];

        $pricePerDay = $service === 'driver'
            ? (float) ($vehicle->with_driver_price_per_day ?? $vehicle->price_per_day ?? 0)
            : (float) ($vehicle->price_per_day ?? 0);

        $estimatedTotal = $days * $pricePerDay;

        return view('user.pages.booking-checkout', compact('vehicle', 'data', 'days', 'estimatedTotal'));
    }

    // 3) CONFIRM BOOKING (POST)
    public function store(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'service'          => ['required', 'in:self,driver'],
            'pickup_location'  => ['required', 'string', 'max:255'],
            'drop_location'    => ['required', 'string', 'max:255'],
            'pickup_datetime'  => ['required', 'date'],
            'drop_datetime'    => ['required', 'date', 'after:pickup_datetime'],
        ]);

        if ($vehicle->status !== 'available') {
            return back()->withErrors(['vehicle' => 'Vehicle is not available.']);
        }

        $pickup = Carbon::parse($data['pickup_datetime']);
        $drop   = Carbon::parse($data['drop_datetime']);

        $overlap = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('pickup_datetime', '<=', $drop)
            ->where('drop_datetime', '>=', $pickup)
            ->exists();

        if ($overlap) {
            return back()->withErrors(['dates' => 'Vehicle already booked for selected time.']);
        }

        $hours = $pickup->diffInHours($drop);
        $days  = max(1, (int) ceil($hours / 24));

        $pricePerDay = $data['service'] === 'driver'
            ? (float) ($vehicle->with_driver_price_per_day ?? $vehicle->price_per_day ?? 0)
            : (float) ($vehicle->price_per_day ?? 0);

        $total = $days * $pricePerDay;

        $booking = Booking::create([
            'vehicle_id'       => $vehicle->id,
            'user_id'          => Auth::id(),
            'service'          => $data['service'],
            'pickup_location'  => $data['pickup_location'],
            'drop_location'    => $data['drop_location'],
            'pickup_datetime'  => $data['pickup_datetime'],
            'drop_datetime'    => $data['drop_datetime'],
            'status'           => 'confirmed',
            'total_price'      => $total,
        ]);
        $vehicle->update(['status' => 'rented']);
        // For now redirect to a simple success page (later you can do payment)
        return redirect()->route('user.booking.success', $booking->id);
    }

    public function success(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        return view('user.pages.booking-success', compact('booking'));
    }
}
