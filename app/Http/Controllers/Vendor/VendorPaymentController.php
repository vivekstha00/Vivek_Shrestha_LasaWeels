<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class VendorPaymentController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();

        $payments = Payment::with(['user', 'booking.vehicle'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->paginate(10);

        $payments->getCollection()->transform(function (Payment $payment) {
            $payment->original_vehicle_value = $this->calculateOriginalVehicleValue($payment);

            return $payment;
        });

        $earningQuery = Payment::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->where('refund_status', '!=', 'refunded')
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'cancelled');
            });

        $totalCustomerPaid = (clone $earningQuery)->sum('amount');
        $totalCommission = (clone $earningQuery)->sum('platform_commission');
        $totalNet = (clone $earningQuery)->sum('vendor_amount');

        $pendingPayout = Payment::where('vendor_id', $vendorId)
            ->whereIn('payout_status', ['unpaid', 'pending'])
            ->where('refund_status', '!=', 'refunded')
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->sum('vendor_amount');

        $paidPayout = Payment::where('vendor_id', $vendorId)
            ->where('payout_status', 'paid')
            ->where('refund_status', '!=', 'refunded')
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->sum('vendor_amount');

        $totalLoyaltyDiscount = Payment::where('vendor_id', $vendorId)
            ->where('payments.status', 'completed')
            ->where('payments.refund_status', '!=', 'refunded')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.status', '!=', 'cancelled')
            ->sum('bookings.loyalty_discount_amount');

        $totalOriginalVehicleValue = Payment::with(['booking.vehicle'])
            ->where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->where('refund_status', '!=', 'refunded')
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->get()
            ->sum(function (Payment $payment) {
                return $this->calculateOriginalVehicleValue($payment);
            });

        $totalOriginalValue = $totalOriginalVehicleValue;

        return view('vendor.pages.payments.index', compact(
            'payments',
            'totalCustomerPaid',
            'totalCommission',
            'totalNet',
            'pendingPayout',
            'paidPayout',
            'totalLoyaltyDiscount',
            'totalOriginalValue',
            'totalOriginalVehicleValue'
        ));
    }

    public function show(Payment $payment)
    {
        abort_unless($payment->vendor_id === Auth::id(), 403);

        $payment->load(['user', 'booking.vehicle']);

        return view('vendor.pages.payments.show', compact('payment'));
    }

    private function calculateOriginalVehicleValue(Payment $payment): float
    {
        $booking = $payment->booking;

        if (! $booking || ! $booking->vehicle) {
            return 0;
        }

        $pickup = $booking->pickup_datetime;
        $drop = $booking->drop_datetime;

        if (! $pickup || ! $drop) {
            return 0;
        }

        $totalMinutes = max(0, $pickup->diffInMinutes($drop));
        $minutesPerDay = 24 * 60;
        $fullDays = intdiv($totalMinutes, $minutesPerDay);
        $remainingMinutes = $totalMinutes % $minutesPerDay;
        $graceMinutes = (int) config('vehicle.billing_grace_hours', 2) * 60;

        if ($remainingMinutes === 0) {
            $days = max(1, $fullDays);
        } elseif ($remainingMinutes <= $graceMinutes) {
            $days = max(1, $fullDays);
        } else {
            $days = max(1, $fullDays + 1);
        }

        $dailyRate = $booking->service === 'driver'
            ? ($booking->vehicle->with_driver_price_per_day ?? $booking->vehicle->price_per_day)
            : $booking->vehicle->price_per_day;

        return round((float) $dailyRate * $days, 2);
    }
}
