<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    // LIST all payments
    public function index(Request $request)
    {
        $query = Payment::query()->with([
            'user',
            'booking.vehicle',
        ]);

        // Filters
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('booking_id', $q)
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%"));
            });
        }

        $payments = $query->latest()->paginate(10)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    // SHOW a payment
    public function show(Payment $payment)
    {
        $payment->load(['user', 'booking.vehicle', 'booking.driver']);
        return view('admin.payments.show', compact('payment'));
    }

    // UPDATE status (cash completion / mark failed / etc.)
    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
            'payout_status' => 'nullable|in:unpaid,paid,pending',
        ]);

        $payment->update([
            'status' => $data['status'],
            'payout_status' => $data['payout_status'] ?? $payment->payout_status,
        ]);

        // Sync booking payment_status when payment completed
        if ($payment->booking) {
            if ($payment->status === 'completed') {
                $payment->booking->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                ]);
            }

            if (in_array($payment->status, ['failed', 'pending'])) {
                $payment->booking->update([
                    'payment_status' => 'unpaid',
                ]);
            }
        }

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Payment updated successfully.');
    }
}
