<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class VendorPaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'booking.vehicle'])
            ->where('vendor_id', Auth::id())
            ->latest()
            ->paginate(10);

        $totalGross = Payment::where('vendor_id', Auth::id())->sum('amount');
        $totalCommission = Payment::where('vendor_id', Auth::id())->sum('platform_commission');
        $totalNet = Payment::where('vendor_id', Auth::id())->sum('vendor_amount');

        $pendingPayout = Payment::where('vendor_id', Auth::id())
            ->whereIn('payout_status', ['unpaid', 'pending'])
            ->sum('vendor_amount');

        $paidPayout = Payment::where('vendor_id', Auth::id())
            ->where('payout_status', 'paid')
            ->sum('vendor_amount');

        return view('vendor.pages.payments.index', compact(
            'payments',
            'totalGross',
            'totalCommission',
            'totalNet',
            'pendingPayout',
            'paidPayout'
        ));
    }

    public function show(Payment $payment)
    {
        abort_unless($payment->vendor_id === Auth::id(), 403);

        $payment->load(['user', 'booking.vehicle']);

        return view('vendor.pages.payments.show', compact('payment'));
    }
}
