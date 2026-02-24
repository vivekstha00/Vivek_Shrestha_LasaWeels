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
            // base search
            'service'          => ['required', 'in:self,driver'],
            'pickup_location'  => ['required', 'string', 'max:255'],
            'drop_location'    => ['required', 'string', 'max:255'],
            'pickup_datetime'  => ['required', 'date'],
            'drop_datetime'    => ['required', 'date', 'after:pickup_datetime'],

            // ✅ filters (optional)
            'vehicle_type'     => ['nullable', 'string', 'max:50'],
            'fuel_type'        => ['nullable', 'string', 'max:50'],
            'transmission'     => ['nullable', 'string', 'max:50'],
            'wheel_type'       => ['nullable', 'string', 'max:50'],
            'price_sort'       => ['nullable', 'in:low_high,high_low'],
        ]);

        $pickup = Carbon::parse($data['pickup_datetime']);
        $drop   = Carbon::parse($data['drop_datetime']);

        // for sorting: if service=driver use with_driver_price_per_day else price_per_day
        $sortColumn = $data['service'] === 'driver'
            ? 'with_driver_price_per_day'
            : 'price_per_day';

        $vehicles = Vehicle::query()
            ->with(['primaryImage', 'images'])

            // ✅ your availability logic
            ->where('status', 'available')
            ->whereDoesntHave('bookings', function ($q) use ($pickup, $drop) {
                $q->whereIn('status', ['pending', 'confirmed'])
                ->where('pickup_datetime', '<=', $drop)
                ->where('drop_datetime', '>=', $pickup);
            })

            // ✅ filters
            ->when(!empty($data['vehicle_type']), fn ($q) =>
                $q->where('vehicle_type', $data['vehicle_type'])
            )
            ->when(!empty($data['fuel_type']), fn ($q) =>
                $q->where('fuel_type', $data['fuel_type'])
            )
            ->when(!empty($data['transmission']), fn ($q) =>
                $q->where('transmission', $data['transmission'])
            )
            ->when(!empty($data['wheel_type']), fn ($q) =>
                $q->where('wheel_type', $data['wheel_type'])
            )

            // ✅ price sorting
            ->when(!empty($data['price_sort']), function ($q) use ($data, $sortColumn) {
                // if driver price is null, sort by self price as fallback
                // COALESCE(with_driver_price_per_day, price_per_day)
                if ($sortColumn === 'with_driver_price_per_day') {
                    $direction = $data['price_sort'] === 'low_high' ? 'asc' : 'desc';
                    return $q->orderByRaw("COALESCE(with_driver_price_per_day, price_per_day) {$direction}");
                }

                return $q->orderBy(
                    'price_per_day',
                    $data['price_sort'] === 'low_high' ? 'asc' : 'desc'
                );
            })

            // fallback ordering if no price_sort
            ->latest('id')

            ->paginate(5)
            ->withQueryString();

        return view('user.pages.search-results', [
            'vehicles' => $vehicles,
            'search'   => $data,
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
            'special_request'  => ['nullable', 'string', 'max:1000'],
        ]);

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
        $days  = max(1, ceil($hours / 24));

        $pricePerDay = $data['service'] === 'driver'
            ? ($vehicle->with_driver_price_per_day ?? $vehicle->price_per_day)
            : $vehicle->price_per_day;

        $estimatedTotal = $days * $pricePerDay;

        $securityDeposit = $data['service'] === 'self' ? $vehicle->security_deposit : 0;

        return view('user.pages.booking-checkouts', compact(
            'vehicle', 'data', 'days', 'estimatedTotal', 'securityDeposit'
        ));
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
            'special_request'  => ['nullable', 'string', 'max:1000'],
        ]);

        $pickup = Carbon::parse($data['pickup_datetime']);
        $drop   = Carbon::parse($data['drop_datetime']);

        // Overlap check again
        $overlap = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('pickup_datetime', '<=', $drop)
            ->where('drop_datetime', '>=', $pickup)
            ->exists();

        if ($overlap) {
            return back()->withErrors(['dates' => 'Vehicle already booked for selected time.']);
        }

        $hours = $pickup->diffInHours($drop);
        $days  = max(1, ceil($hours / 24));

        $pricePerDay = $data['service'] === 'driver'
            ? ($vehicle->with_driver_price_per_day ?? $vehicle->price_per_day)
            : $vehicle->price_per_day;

        $total = $days * $pricePerDay;

        $securityDeposit = $data['service'] === 'self'
            ? $vehicle->security_deposit
            : null;

        $booking = Booking::create([
            'vehicle_id'       => $vehicle->id,
            'user_id'          => Auth::id(),
            'service'          => $data['service'],
            'pickup_location'  => $data['pickup_location'],
            'drop_location'    => $data['drop_location'],
            'pickup_datetime'  => $pickup,
            'drop_datetime'    => $drop,
            'special_request'  => $data['special_request'] ?? null,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',    
            'total_price'      => $total,
            'security_deposit' => $securityDeposit,
        ]);

        return redirect()->route('user.booking.success', $booking->id);
    }

    public function success(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        return view('user.pages.booking-success', compact('booking'));
    }
}
