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
            ->where('status', 'completed')
            ->where('refund_status', '!=', 'refunded')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.status', '!=', 'cancelled')
            ->sum('bookings.loyalty_discount_amount');

        $totalOriginalValue = $totalCustomerPaid + $totalLoyaltyDiscount;

        return view('vendor.pages.payments.index', compact(
            'payments',
            'totalCustomerPaid',
            'totalCommission',
            'totalNet',
            'pendingPayout',
            'paidPayout',
            'totalLoyaltyDiscount',
            'totalOriginalValue'
        ));
    }

    public function show(Payment $payment)
    {
        abort_unless($payment->vendor_id === Auth::id(), 403);

        $payment->load(['user', 'booking.vehicle']);

        return view('vendor.pages.payments.show', compact('payment'));
    }
}
