<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with([
            'user',
            'vendor',
            'booking.vehicle',
        ]);

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('booking_id', $q)
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhereHas('vendor', function ($v) use ($q) {
                        $v->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $commissionRevenue = Payment::query()
            ->where('status', 'completed')
            ->where(function ($sub) {
                $sub->whereNull('refund_status')
                    ->orWhere('refund_status', '!=', 'refunded');
            })
            ->sum('platform_commission');

        $subscriptionRevenue = SubscriptionPayment::query()
            ->where('status', 'completed')
            ->sum('amount');

        $totalPlatformRevenue = $commissionRevenue + $subscriptionRevenue;

        $successfulCommissionPayments = Payment::query()
            ->where('status', 'completed')
            ->where(function ($sub) {
                $sub->whereNull('refund_status')
                    ->orWhere('refund_status', '!=', 'refunded');
            })
            ->count();

        $successfulSubscriptionPayments = SubscriptionPayment::query()
            ->where('status', 'completed')
            ->count();

        $payments = $query->latest()->paginate(10)->withQueryString();

        return view('admin.payments.index', compact(
            'payments',
            'commissionRevenue',
            'subscriptionRevenue',
            'totalPlatformRevenue',
            'successfulCommissionPayments',
            'successfulSubscriptionPayments'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load([
            'user',
            'vendor',
            'booking.vehicle',
            'booking.driver',
        ]);

        return view('admin.payments.show', compact('payment'));
    }

    public function commissions(Request $request)
    {
        $query = Payment::query()
            ->with(['user', 'vendor', 'booking'])
            ->where('platform_commission', '>', 0)
            ->where('status', 'completed');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('booking_id', $q)
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhereHas('vendor', function ($v) use ($q) {
                        $v->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $commissionPayments = $query->latest()->paginate(12)->withQueryString();

        $totalCommission = Payment::query()
            ->where('platform_commission', '>', 0)
            ->where('status', 'completed')
            ->where(function ($sub) {
                $sub->whereNull('refund_status')
                    ->orWhere('refund_status', '!=', 'refunded');
            })
            ->sum('platform_commission');

        $commissionTransactionsCount = Payment::query()
            ->where('platform_commission', '>', 0)
            ->where('status', 'completed')
            ->count();

        return view('admin.payments.commissions', compact(
            'commissionPayments',
            'totalCommission',
            'commissionTransactionsCount'
        ));
    }

    public function subscriptions(Request $request)
    {
        $query = SubscriptionPayment::query()
            ->with(['vendor', 'plan'])
            ->where('status', 'completed');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('purchase_order_id', 'like', "%{$q}%")
                    ->orWhere('gateway_reference', 'like', "%{$q}%")
                    ->orWhereHas('vendor', function ($vendor) use ($q) {
                        $vendor->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $subscriptionPayments = $query->latest()->paginate(12)->withQueryString();

        $totalSubscriptionRevenue = SubscriptionPayment::query()
            ->where('status', 'completed')
            ->sum('amount');

        $subscriptionTransactionsCount = SubscriptionPayment::query()
            ->where('status', 'completed')
            ->count();

        return view('admin.payments.subscriptions', compact(
            'subscriptionPayments',
            'totalSubscriptionRevenue',
            'subscriptionTransactionsCount'
        ));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
            'payout_status' => 'nullable|in:unpaid,pending,paid,hold',
            'deposit_status' => 'nullable|in:pending,paid,refunded,forfeited',
            'settlement_status' => 'nullable|in:pending_balance,balance_received,payout_pending,paid_to_vendor,not_applicable,refunded',
        ]);

        $isRefundedAction =
            ($data['status'] ?? null) === 'refunded' ||
            ($data['settlement_status'] ?? null) === 'refunded' ||
            $payment->refund_status === 'refunded';

        if ($isRefundedAction) {
            $payment->update([
                'status' => 'refunded',
                'refund_status' => 'refunded',
                'refund_processed_at' => $payment->refund_processed_at ?? now(),
                'payout_status' => 'hold',
                'settlement_status' => 'refunded',
                'deposit_status' => $payment->payment_type === 'deposit_cash' ? 'refunded' : $payment->deposit_status,
                'remaining_amount' => 0,
            ]);

            if ($payment->booking) {
                $payment->booking->update([
                    'status' => 'cancelled',
                ]);
            }

            return redirect()
                ->route('admin.payments.show', $payment->id)
                ->with('success', 'Refunded payment updated successfully.');
        }

        $payment->update([
            'status' => $data['status'],
            'payout_status' => $data['payout_status'] ?? $payment->payout_status,
            'deposit_status' => $data['deposit_status'] ?? $payment->deposit_status,
            'settlement_status' => $data['settlement_status'] ?? $payment->settlement_status,
        ]);

        if ($payment->booking) {
            if ($payment->payment_type === 'full_online') {
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

            if ($payment->payment_type === 'deposit_cash') {
                if ($payment->status === 'completed' && $payment->settlement_status === 'balance_received') {
                    $payment->update([
                        'paid_amount' => $payment->amount,
                        'remaining_amount' => 0,
                    ]);

                    $payment->booking->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                    ]);
                } elseif ($payment->status === 'completed') {
                    $payment->booking->update([
                        'payment_status' => 'partial',
                        'status' => 'confirmed',
                    ]);
                }

                if (in_array($payment->status, ['failed', 'pending'])) {
                    $payment->booking->update([
                        'payment_status' => 'unpaid',
                    ]);
                }
            }
        }

        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Payment updated successfully.');
    }
}
