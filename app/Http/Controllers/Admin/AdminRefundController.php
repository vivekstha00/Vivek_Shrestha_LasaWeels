<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminRefundController extends Controller
{
    public function index()
    {
        $refundPayments = Payment::with(['booking.user', 'booking.vehicle'])
            ->whereIn('refund_status', ['pending', 'refunded', 'rejected'])
            ->orderByRaw("CASE refund_status WHEN 'pending' THEN 0 WHEN 'rejected' THEN 1 ELSE 2 END")
            ->latest('refund_requested_at')
            ->paginate(10);

        return view('admin.refunds.index', compact('refundPayments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.user', 'booking.vehicle', 'vendor']);

        return view('admin.refunds.show', compact('payment'));
    }

    public function approve(Request $request, Payment $payment)
    {
        $request->validate([
            'refund_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->load('booking');

        if (! $payment->booking) {
            return back()->withErrors([
                'refund' => 'Related booking not found.',
            ]);
        }

        if ($payment->refund_status !== 'pending') {
            return back()->withErrors([
                'refund' => 'Only pending refund requests can be approved.',
            ]);
        }

        DB::transaction(function () use ($payment, $request) {
            $booking = $payment->booking;

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
            ]);

            $payment->update([
                'status' => 'refunded',
                'refund_status' => 'refunded',
                'refund_processed_at' => now(),
                'refund_note' => $request->refund_note ?: $payment->refund_note,
                'settlement_status' => 'refunded',
                'payout_status' => 'hold',
                'remaining_amount' => 0,
                'deposit_status' => $payment->payment_type === 'deposit_cash' ? 'refunded' : $payment->deposit_status,
            ]);

            if ((int) $booking->loyalty_points_redeemed > 0) {
                app(LoyaltyService::class)->restoreRedeemedPoints($booking->fresh());
            }
        });

        return redirect()
            ->route('admin.refunds.index')
            ->with('success', 'Refund approved successfully.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'refund_note' => ['required', 'string', 'max:1000'],
        ]);

        $payment->load('booking');

        if (! $payment->booking) {
            return back()->withErrors([
                'refund' => 'Related booking not found.',
            ]);
        }

        if ($payment->refund_status !== 'pending') {
            return back()->withErrors([
                'refund' => 'Only pending refund requests can be rejected.',
            ]);
        }

        DB::transaction(function () use ($payment, $request) {
            $booking = $payment->booking;

            $booking->update([
                'status' => 'confirmed',
            ]);

            $payment->update([
                'status' => 'completed',
                'refund_status' => 'rejected',
                'refund_processed_at' => now(),
                'refund_note' => $request->refund_note,
                'settlement_status' => 'payout_pending',
                'payout_status' => 'unpaid',
                'deposit_status' => $payment->payment_type === 'deposit_cash' ? 'paid' : $payment->deposit_status,
            ]);
        });

        return redirect()
            ->route('admin.refunds.index')
            ->with('success', 'Refund request rejected successfully.');
    }
}
