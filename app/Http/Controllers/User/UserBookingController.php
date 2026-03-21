<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BookingSuccessNotification;
use App\Notifications\BookingRequestToAdminNotification;
use App\Notifications\BookingRequestToVendorNotification;
use Illuminate\Support\Facades\Notification;
use App\Services\LoyaltyService;

class UserBookingController extends Controller
{
    public function index(Request $request)
    {
        $loyaltyService = app(LoyaltyService::class);

        $bookingsToComplete = Booking::where('user_id', Auth::id())
            ->whereIn('status', ['confirmed', 'active'])
            ->where('drop_datetime', '<', Carbon::now())
            ->get();

        foreach ($bookingsToComplete as $bookingToComplete) {
            $bookingToComplete->update([
                'status' => 'completed',
            ]);

            $loyaltyService->awardCompletedBookingPoints($bookingToComplete->fresh());
        }

        $query = Booking::with(['vehicle', 'payment', 'driver', 'review'])
            ->where('user_id', Auth::id());

        if ($request->filled('from_date')) {
            $query->whereDate('pickup_datetime', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('drop_datetime', '<=', $request->to_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('service')) {
            $query->where('service', $request->service);
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('user.pages.booking.my-bookings', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load(['vehicle', 'payment', 'driver']);

        return view('user.pages.booking.booking-show', compact('booking'));
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'service'          => ['required', 'in:self,driver'],
            'pickup_location'  => ['required', 'string', 'max:255'],
            'drop_location'    => ['required', 'string', 'max:255'],
            'pickup_datetime'  => ['required', 'date'],
            'drop_datetime'    => ['required', 'date', 'after:pickup_datetime'],
            'vehicle_type'     => ['nullable', 'string', 'max:50'],
            'fuel_type'        => ['nullable', 'string', 'max:50'],
            'transmission'     => ['nullable', 'string', 'max:50'],
            'wheel_type'       => ['nullable', 'string', 'max:50'],
            'price_sort'       => ['nullable', 'in:low_high,high_low'],
        ]);

        $pickup = Carbon::parse($data['pickup_datetime']);
        $drop   = Carbon::parse($data['drop_datetime']);

        $sortColumn = $data['service'] === 'driver'
            ? 'with_driver_price_per_day'
            : 'price_per_day';

        $vehicles = Vehicle::query()
            ->with(['primaryImage', 'images'])
            ->where('status', 'available')
            ->whereDoesntHave('bookings', function ($q) use ($pickup, $drop) {
                $q->whereIn('status', ['pending', 'confirmed'])
                    ->where('pickup_datetime', '<=', $drop)
                    ->where('drop_datetime', '>=', $pickup);
            })
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
            ->when(!empty($data['price_sort']), function ($q) use ($data, $sortColumn) {
                if ($sortColumn === 'with_driver_price_per_day') {
                    $direction = $data['price_sort'] === 'low_high' ? 'asc' : 'desc';
                    return $q->orderByRaw("COALESCE(with_driver_price_per_day, price_per_day) {$direction}");
                }

                return $q->orderBy(
                    'price_per_day',
                    $data['price_sort'] === 'low_high' ? 'asc' : 'desc'
                );
            })
            ->latest('id')
            ->paginate(5)
            ->withQueryString();

        return view('user.pages.vehicles.search-results', [
            'vehicles' => $vehicles,
            'search'   => $data,
        ]);
    }

    private function redirectSelfDriveVerification()
    {
        return redirect()
            ->to(route('user.profile') . '#documents-section')
            ->with('error', 'To book a self-drive vehicle, your license and citizenship must be approved first.');
    }

    public function create(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'service'          => ['required', 'in:self,driver'],
            'pickup_location'  => ['required', 'string', 'max:255'],
            'drop_location'    => ['required', 'string', 'max:255'],
            'pickup_datetime'  => ['required', 'date'],
            'drop_datetime'    => ['required', 'date', 'after:pickup_datetime'],
            'special_request'  => ['nullable', 'string', 'max:1000'],
            'driver_id'        => ['nullable', 'exists:drivers,id'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($data['service'] === 'self' && ! $user->hasApprovedSelfDriveDocuments()) {
            return $this->redirectSelfDriveVerification();
        }

        $pickup = Carbon::parse($data['pickup_datetime']);
        $drop   = Carbon::parse($data['drop_datetime']);

        $overlap = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('pickup_datetime', '<=', $drop)
            ->where('drop_datetime', '>=', $pickup)
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'dates' => 'Vehicle already booked for selected time.'
            ])->withInput();
        }

        $hours = $pickup->diffInHours($drop);
        $days  = max(1, ceil($hours / 24));

        $pricePerDay = $data['service'] === 'driver'
            ? ($vehicle->with_driver_price_per_day ?? $vehicle->price_per_day)
            : $vehicle->price_per_day;

        $estimatedTotal = $days * $pricePerDay;

        $loyaltyService = app(LoyaltyService::class);
        $maxRedeemablePoints = $loyaltyService->getMaxRedeemablePoints($user, $estimatedTotal);
        $availablePoints = $user->loyaltyAccount->available_points ?? 0;

        $securityDeposit = $data['service'] === 'self'
            ? $vehicle->security_deposit
            : 0;

        $service = $data['service'];

        $selectedDriver = null;
        if ($service === 'driver' && ! empty($data['driver_id'])) {
            $selectedDriver = Driver::find($data['driver_id']);
        }

        return view('user.pages.booking.booking-checkouts', compact(
            'vehicle',
            'data',
            'days',
            'estimatedTotal',
            'securityDeposit',
            'service',
            'selectedDriver',
            'availablePoints',
            'maxRedeemablePoints'
        ));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'service'          => ['required', 'in:self,driver'],
            'pickup_location'  => ['required', 'string', 'max:255'],
            'drop_location'    => ['required', 'string', 'max:255'],
            'pickup_datetime'  => ['required', 'date'],
            'drop_datetime'    => ['required', 'date', 'after:pickup_datetime'],
            'special_request'  => ['nullable', 'string', 'max:1000'],
            'driver_id'        => [$request->service === 'driver' ? 'required' : 'nullable', 'exists:drivers,id'],
            'redeem_points'    => ['nullable', 'integer', 'min:0'],
            'accept_terms' => ['required', 'accepted'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($data['service'] === 'self' && ! $user->hasApprovedSelfDriveDocuments()) {
            return $this->redirectSelfDriveVerification();
        }

        $pickup = Carbon::parse($data['pickup_datetime']);
        $drop   = Carbon::parse($data['drop_datetime']);

        $overlap = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('pickup_datetime', '<=', $drop)
            ->where('drop_datetime', '>=', $pickup)
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'dates' => 'Vehicle already booked for selected time.'
            ])->withInput();
        }

        if ($data['service'] === 'driver' && ! empty($data['driver_id'])) {
            $driverBusy = Booking::where('driver_id', $data['driver_id'])
                ->whereIn('status', ['pending', 'confirmed', 'active'])
                ->where('pickup_datetime', '<=', $drop)
                ->where('drop_datetime', '>=', $pickup)
                ->exists();

            if ($driverBusy) {
                return back()
                    ->withErrors([
                        'driver_id' => 'This driver is already booked for the selected time. Please choose another driver.'
                    ])
                    ->withInput();
            }
        }

        $hours = $pickup->diffInHours($drop);
        $days  = max(1, ceil($hours / 24));

        $pricePerDay = $data['service'] === 'driver'
            ? ($vehicle->with_driver_price_per_day ?? $vehicle->price_per_day)
            : $vehicle->price_per_day;

        $totalPrice = $days * $pricePerDay;

        $loyaltyService = app(LoyaltyService::class);
        $requestedRedeemPoints = (int) ($data['redeem_points'] ?? 0);
        $maxRedeemablePoints = $loyaltyService->getMaxRedeemablePoints($user, $totalPrice);

        if ($requestedRedeemPoints > 0) {
            if ($requestedRedeemPoints < 100) {
                return back()
                    ->withErrors([
                        'redeem_points' => 'Minimum redeemable points is 100.'
                    ])
                    ->withInput();
            }

            if ($requestedRedeemPoints > $maxRedeemablePoints) {
                return back()
                    ->withErrors([
                        'redeem_points' => 'Requested redeem points exceed your allowed limit.'
                    ])
                    ->withInput();
            }
        }

        $loyaltyDiscountAmount = $requestedRedeemPoints; // 1 point = 1 NPR
        $finalTotalPrice = max(0, $totalPrice - $loyaltyDiscountAmount);

        $securityDeposit = $data['service'] === 'self'
            ? $vehicle->security_deposit
            : null;

        $booking = Booking::create([
            'vehicle_id'               => $vehicle->id,
            'user_id'                  => Auth::id(),
            'service'                  => $data['service'],
            'pickup_location'          => $data['pickup_location'],
            'drop_location'            => $data['drop_location'],
            'pickup_datetime'          => $pickup,
            'drop_datetime'            => $drop,
            'special_request'          => $data['special_request'] ?? null,
            'status'                   => 'pending',
            'payment_status'           => 'unpaid',
            'total_price'              => $finalTotalPrice,
            'loyalty_points_redeemed'  => $requestedRedeemPoints,
            'loyalty_discount_amount'  => $loyaltyDiscountAmount,
            'security_deposit'         => $securityDeposit,
            'driver_id'                => $data['service'] === 'driver' ? ($data['driver_id'] ?? null) : null,
        ]);

        $booking->user->notify(new BookingSuccessNotification($booking));

        $vendorUser = $vehicle->vendor;
        if ($vendorUser && ! empty($vendorUser->email)) {
            $vendorUser->notify(new BookingRequestToVendorNotification($booking));
        }

        Notification::route('mail', config('app.admin_email'))
            ->notify(new BookingRequestToAdminNotification($booking));

        return redirect()->route('booking.payment', $booking->id);
    }

    public function success(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        return view('user.pages.booking.booking-success', compact('booking'));
    }
}
