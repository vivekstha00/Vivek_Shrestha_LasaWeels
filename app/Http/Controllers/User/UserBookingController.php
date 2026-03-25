<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\DiscountCode;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
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

        $hours = $pickup->diffInHours($drop);
        $days  = max(1, ceil($hours / 24));

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

        $durationPricing = $this->getDurationDiscountBreakdown($vehicle, $days, (float) $pricePerDay);

        $basePrice = $durationPricing['base_price'];
        $durationDiscountPercent = $durationPricing['duration_discount_percent'];
        $durationDiscountAmount = $durationPricing['duration_discount_amount'];
        $estimatedTotal = $durationPricing['price_after_duration_discount'];

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

        $activeDiscountCodes = DiscountCode::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->orderByDesc('id')
            ->get();

        return view('user.pages.booking.booking-checkouts', compact(
            'vehicle',
            'data',
            'days',
            'estimatedTotal',
            'basePrice',
            'durationDiscountPercent',
            'durationDiscountAmount',
            'securityDeposit',
            'service',
            'selectedDriver',
            'availablePoints',
            'maxRedeemablePoints',
            'activeDiscountCodes'
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
            'discount_choice'  => ['nullable', 'in:none,loyalty,code'],
            'redeem_points'    => ['nullable', 'integer', 'min:0'],
            'discount_code'    => ['nullable', 'string', 'max:50'],
            'accept_terms'     => ['required', 'accepted'],
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

        $durationPricing = $this->getDurationDiscountBreakdown($vehicle, $days, (float) $pricePerDay);

        $basePrice = $durationPricing['base_price'];
        $durationDiscountPercent = $durationPricing['duration_discount_percent'];
        $durationDiscountAmount = $durationPricing['duration_discount_amount'];

        $originalPrice = $durationPricing['price_after_duration_discount'];

        $loyaltyService = app(LoyaltyService::class);

        $discountChoice = $data['discount_choice'] ?? 'none';
        $requestedRedeemPoints = (int) ($data['redeem_points'] ?? 0);
        $enteredDiscountCode = strtoupper(trim($data['discount_code'] ?? ''));

        $discountType = 'none';
        $discountCode = null;
        $discountAmount = 0;
        $loyaltyPointsRedeemed = 0;
        $loyaltyDiscountAmount = 0;

        if ($discountChoice === 'loyalty') {
            $maxRedeemablePoints = $loyaltyService->getMaxRedeemablePoints($user, $originalPrice);

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

                $discountType = 'loyalty';
                $discountAmount = $requestedRedeemPoints; // 1 point = 1 NPR
                $loyaltyPointsRedeemed = $requestedRedeemPoints;
                $loyaltyDiscountAmount = $requestedRedeemPoints;
            }
        }

        if ($discountChoice === 'code') {
            if (empty($enteredDiscountCode)) {
                return back()
                    ->withErrors([
                        'discount_code' => 'Please enter a discount code.'
                    ])
                    ->withInput();
            }

            $code = DiscountCode::whereRaw('UPPER(code) = ?', [$enteredDiscountCode])->first();

            if (! $code || ! $code->isUsable()) {
                return back()
                    ->withErrors([
                        'discount_code' => 'This discount code is invalid or expired.'
                    ])
                    ->withInput();
            }

            $discountType = 'code';
            $discountCode = $code->code;
            $discountAmount = $code->calculateDiscount($originalPrice);
        }

        $finalTotalPrice = max(0, $originalPrice - $discountAmount);

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

            'original_price'           => $originalPrice,
            'discount_type'            => $discountType,
            'discount_code'            => $discountCode,
            'discount_amount'          => $discountAmount,
            'total_price'              => $finalTotalPrice,

            'loyalty_points_redeemed'  => $loyaltyPointsRedeemed,
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

    private function canRequestCancellation(Booking $booking): bool
    {
        if (! in_array($booking->status, ['pending', 'confirmed'], true)) {
            return false;
        }

        return now()->lt($booking->pickup_datetime->copy()->subDay());
    }

    private function calculateRefundAmount(?Payment $payment): float
    {
        if (! $payment) {
            return 0;
        }

        if ((float) $payment->paid_amount > 0) {
            return (float) $payment->paid_amount;
        }

        if ((float) $payment->deposit_amount > 0) {
            return (float) $payment->deposit_amount;
        }

        return 0;
    }

    public function requestCancellation(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:1000'],
        ]);

        $booking->load('payment');

        if (! $this->canRequestCancellation($booking)) {
            return back()->withErrors([
                'cancel' => 'Booking cannot be cancelled within 24 hours of pickup or after it has started.',
            ]);
        }

        if ($booking->status === 'cancel_requested') {
            return back()->withErrors([
                'cancel' => 'Cancellation request has already been submitted.',
            ]);
        }

        $payment = $booking->payment;
        $refundAmount = $this->calculateRefundAmount($payment);

        DB::transaction(function () use ($booking, $payment, $refundAmount, $request) {
            if ($payment && $refundAmount > 0) {
                $booking->update([
                    'status' => 'cancel_requested',
                    'cancellation_requested_at' => now(),
                    'cancellation_reason' => $request->cancellation_reason,
                ]);

                $payment->update([
                    'refund_amount' => $refundAmount,
                    'refund_status' => 'pending',
                    'refund_requested_at' => now(),
                    'refund_note' => $request->cancellation_reason,
                    'settlement_status' => 'refund_pending',
                    'payout_status' => 'hold',
                ]);
            } else {
                $booking->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancelled_by' => Auth::id(),
                    'cancellation_requested_at' => now(),
                    'cancellation_reason' => $request->cancellation_reason,
                ]);

                if ($payment) {
                    $payment->update([
                        'refund_amount' => 0,
                        'refund_status' => 'none',
                        'refund_note' => $request->cancellation_reason,
                        'settlement_status' => 'cancelled',
                        'payout_status' => 'hold',
                    ]);
                }
            }
        });

        return redirect()
            ->route('user.booking.show', $booking->id)
            ->with(
                'success',
                $refundAmount > 0
                    ? 'Cancellation request submitted successfully. Refund is pending admin review.'
                    : 'Booking cancelled successfully.'
            );
    }

    private function getDurationDiscountBreakdown(Vehicle $vehicle, int $days, float $pricePerDay): array
    {
        $basePrice = round($days * $pricePerDay, 2);

        $durationDiscountPercent = (float) $vehicle->getDurationDiscountPercent($days);
        $durationDiscountAmount = round($basePrice * ($durationDiscountPercent / 100), 2);

        $priceAfterDurationDiscount = max(0, $basePrice - $durationDiscountAmount);

        return [
            'base_price' => $basePrice,
            'duration_discount_percent' => $durationDiscountPercent,
            'duration_discount_amount' => $durationDiscountAmount,
            'price_after_duration_discount' => $priceAfterDurationDiscount,
        ];
    }

}
