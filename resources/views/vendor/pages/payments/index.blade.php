@extends('vendor.layouts.master')

@section('title', 'My Payments')
@section('page_title', 'My Payments')
@section('page_subtitle', 'Track customer payments, commission, and payouts')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">My Payments</h2>
    <p class="text-muted mb-0">Track customer payments, loyalty discount impact, commission, and payouts</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Original Booking Value</small>
                <h4 class="fw-bold mt-2">Rs. {{ number_format($totalOriginalValue ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Loyalty Discount</small>
                <h4 class="fw-bold text-danger mt-2">Rs. {{ number_format($totalLoyaltyDiscount ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Customer Paid</small>
                <h4 class="fw-bold mt-2">Rs. {{ number_format($totalCustomerPaid ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Net Earnings</small>
                <h4 class="fw-bold mt-2">Rs. {{ number_format($totalNet ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="alert alert-info mb-0">
            Loyalty discounts are platform-funded. Refunded or cancelled bookings are excluded from payout totals.
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Booking</th>
                        <th>Customer</th>
                        <th>Payment Type</th>
                        <th>Original</th>
                        <th>Discount</th>
                        <th>Customer Paid</th>
                        <th>Commission</th>
                        <th>Net Amount</th>
                        <th>Payout</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php
                            $discount = (float) ($payment->booking->loyalty_discount_amount ?? 0);
                            $original = (float) $payment->amount + $discount;
                            $isRefunded = $payment->refund_status === 'refunded' || ($payment->booking && $payment->booking->status === 'cancelled');
                        @endphp

                        <tr
                            onclick="window.location='{{ route('vendor.payments.show', $payment->id) }}'"
                            style="cursor:pointer;"
                            class="vendor-payment-row {{ $isRefunded ? 'table-danger' : '' }}"
                        >
                            <td class="px-4 fw-semibold">#{{ $payment->id }}</td>
                            <td>
                                <div>#{{ $payment->booking_id }}</div>
                                <small class="text-muted text-capitalize">{{ $payment->booking->status ?? 'N/A' }}</small>
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $payment->user->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $payment->user->email ?? '' }}</small>
                            </td>

                            <td>
                                @if($payment->payment_type === 'deposit_cash')
                                    <span class="badge text-bg-info rounded-pill px-3 py-2">Deposit + Cash</span>
                                @else
                                    <span class="badge text-bg-primary rounded-pill px-3 py-2">Full Online</span>
                                @endif
                            </td>

                            <td>Rs. {{ number_format($original, 2) }}</td>
                            <td class="text-danger">Rs. {{ number_format($discount, 2) }}</td>
                            <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                            <td>Rs. {{ number_format($payment->platform_commission, 2) }}</td>
                            <td>
                                @if($isRefunded)
                                    <span class="text-muted">Blocked</span>
                                @else
                                    Rs. {{ number_format($payment->vendor_amount, 2) }}
                                @endif
                            </td>

                            <td>
                                @if($payment->refund_status === 'pending')
                                    <span class="badge text-bg-warning rounded-pill px-3 py-2">Pending</span>
                                @elseif($payment->refund_status === 'refunded')
                                    <span class="badge text-bg-danger rounded-pill px-3 py-2">Refunded</span>
                                @elseif($payment->refund_status === 'rejected')
                                    <span class="badge text-bg-secondary rounded-pill px-3 py-2">Rejected</span>
                                @else
                                    <span class="badge text-bg-light rounded-pill px-3 py-2">None</span>
                                @endif
                            </td>

                            <td>
                                @if($isRefunded || $payment->payout_status === 'hold')
                                    <span class="badge text-bg-dark rounded-pill px-3 py-2">Blocked</span>
                                @elseif($payment->payout_status === 'paid')
                                    <span class="badge text-bg-success rounded-pill px-3 py-2">Paid</span>
                                @elseif($payment->payout_status === 'pending')
                                    <span class="badge text-bg-warning rounded-pill px-3 py-2">Pending</span>
                                @else
                                    <span class="badge text-bg-secondary rounded-pill px-3 py-2">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">No payment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
