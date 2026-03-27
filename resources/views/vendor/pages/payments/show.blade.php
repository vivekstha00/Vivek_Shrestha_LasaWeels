@extends('vendor.layouts.master')

@section('title', 'Payment Details')
@section('page_title', 'Payment #{{ $payment->id }}')
@section('page_subtitle', 'View earnings, loyalty discount impact, and payout details')

@section('vendor-content')
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Payment #{{ $payment->id }}</h2>
            <p class="text-muted mb-0">View earnings, loyalty discount impact, and payout details</p>
        </div>
        <a href="{{ route('vendor.payments.index') }}" class="btn btn-outline-secondary">
            ← Back to Payments
        </a>
    </div>
</div>

@php
    $loyaltyDiscount = (float) ($payment->booking->loyalty_discount_amount ?? 0);
    $originalAmount = (float) $payment->amount + $loyaltyDiscount;
    $isRefunded = $payment->refund_status === 'refunded' || ($payment->booking && $payment->booking->status === 'cancelled');
@endphp

@if($isRefunded)
    <div class="alert alert-danger mb-4">
        This booking was cancelled and refunded. This payment remains in history, but it is excluded from your payable earnings and vendor payout.
    </div>
@endif

<div class="row g-4">
    <!-- Main Payment Details -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Payment Details</h5>

                <div class="row g-3">
                    <div class="col-md-4 text-muted small">Booking ID</div>
                    <div class="col-md-8 fw-medium">#{{ $payment->booking_id }}</div>

                    <div class="col-md-4 text-muted small">Customer</div>
                    <div class="col-md-8">{{ $payment->user->name ?? 'N/A' }}</div>

                    <div class="col-md-4 text-muted small">Payment Type</div>
                    <div class="col-md-8">
                        {{ $payment->payment_type === 'deposit_cash' ? 'Deposit + Cash' : 'Full Online' }}
                    </div>

                    <div class="col-md-4 text-muted small">Original Booking Amount</div>
                    <div class="col-md-8">Rs. {{ number_format($originalAmount, 2) }}</div>

                    <div class="col-md-4 text-muted small">Loyalty Discount</div>
                    <div class="col-md-8 text-danger">Rs. {{ number_format($loyaltyDiscount, 2) }}</div>

                    <div class="col-md-4 text-muted small">Customer Paid Amount</div>
                    <div class="col-md-8">Rs. {{ number_format($payment->amount, 2) }}</div>

                    <div class="col-md-4 text-muted small">Paid Amount</div>
                    <div class="col-md-8">Rs. {{ number_format($payment->paid_amount, 2) }}</div>

                    <div class="col-md-4 text-muted small">Remaining Amount</div>
                    <div class="col-md-8">Rs. {{ number_format($payment->remaining_amount, 2) }}</div>

                    @if($payment->payment_type === 'deposit_cash')
                        <div class="col-md-4 text-muted small">Deposit Amount</div>
                        <div class="col-md-8">Rs. {{ number_format($payment->deposit_amount, 2) }}</div>
                    @endif
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-4 text-muted small">Platform Commission</div>
                    <div class="col-md-8">Rs. {{ number_format($payment->platform_commission, 2) }}</div>

                    <div class="col-md-4 text-muted small">Your Net Amount</div>
                    <div class="col-md-8 fw-bold">
                        @if($isRefunded)
                            <span class="text-danger">Blocked due to refund</span>
                        @else
                            Rs. {{ number_format($payment->vendor_amount, 2) }}
                        @endif
                    </div>

                    <div class="col-md-4 text-muted small">Payout Status</div>
                    <div class="col-md-8">
                        <span class="badge {{ $payment->payout_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($payment->payout_status ?? 'Unpaid') }}
                        </span>
                    </div>

                    <div class="col-md-4 text-muted small">Settlement Status</div>
                    <div class="col-md-8 fw-medium">
                        {{ ucfirst(str_replace('_', ' ', $payment->settlement_status ?? 'N/A')) }}
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-4 text-muted small">Refund Status</div>
                    <div class="col-md-8">
                        {{ ucfirst($payment->refund_status ?? 'None') }}
                    </div>

                    <div class="col-md-4 text-muted small">Refund Amount</div>
                    <div class="col-md-8">Rs. {{ number_format($payment->refund_amount ?? 0, 2) }}</div>

                    @if($payment->refund_processed_at)
                        <div class="col-md-4 text-muted small">Refund Processed At</div>
                        <div class="col-md-8">{{ $payment->refund_processed_at->format('d M Y, h:i A') }}</div>
                    @endif

                    @if($payment->refund_note)
                        <div class="col-md-4 text-muted small">Refund Note</div>
                        <div class="col-md-8">{{ $payment->refund_note }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vehicle / Booking Info -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Vehicle / Booking Info</h5>
                <div class="row g-3">
                    <div class="col-md-4 text-muted small">Vehicle</div>
                    <div class="col-md-8 fw-medium">
                        {{ $payment->booking->vehicle->brand ?? '' }}
                        {{ $payment->booking->vehicle->model ?? '' }}
                    </div>

                    <div class="col-md-4 text-muted small">Booking Status</div>
                    <div class="col-md-8">{{ ucfirst($payment->booking->status ?? 'N/A') }}</div>

                    <div class="col-md-4 text-muted small">Booking Payment Status</div>
                    <div class="col-md-8">{{ ucfirst($payment->booking->payment_status ?? 'N/A') }}</div>
                </div>

                @if($loyaltyDiscount > 0)
                    <div class="alert alert-info mt-4 mb-0">
                        This booking used loyalty redemption. The discount is funded by the platform.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Note -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Payout Note</h6>
                <p class="text-muted">
                    Loyalty discounts are platform-funded. Refunded bookings do not remain payable to the vendor.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
