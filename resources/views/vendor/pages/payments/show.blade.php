@extends('vendor.layouts.master')

@section('title', 'Payment Details')

@section('vendor-content')
<div class="container-fluid py-4">

    @php
        $loyaltyDiscount = (float) ($payment->booking->loyalty_discount_amount ?? 0);
        $originalAmount = (float) $payment->amount + $loyaltyDiscount;
        $isRefunded = $payment->refund_status === 'refunded' || ($payment->booking && $payment->booking->status === 'cancelled');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Payment #{{ $payment->id }}</h3>
            <p class="text-muted mb-0">View earnings, loyalty discount impact, and payout details</p>
        </div>

        <a href="{{ route('vendor.payments.index') }}" class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    @if($isRefunded)
        <div class="alert alert-danger rounded-3">
            This booking was cancelled and refunded. This payment remains in history, but it is excluded from your payable earnings and vendor payout.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Payment Details</h5>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Booking ID</div>
                        <div class="col-md-8">#{{ $payment->booking_id }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Customer</div>
                        <div class="col-md-8">{{ $payment->user->name ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Payment Type</div>
                        <div class="col-md-8">
                            {{ $payment->payment_type === 'deposit_cash' ? 'Deposit + Cash' : 'Full Online' }}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Original Booking Amount</div>
                        <div class="col-md-8">Rs. {{ number_format($originalAmount, 2) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Loyalty Discount</div>
                        <div class="col-md-8 text-danger">Rs. {{ number_format($loyaltyDiscount, 2) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Customer Paid Amount</div>
                        <div class="col-md-8">Rs. {{ number_format($payment->amount, 2) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Paid Amount</div>
                        <div class="col-md-8">Rs. {{ number_format($payment->paid_amount, 2) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Remaining Amount</div>
                        <div class="col-md-8">Rs. {{ number_format($payment->remaining_amount, 2) }}</div>
                    </div>

                    @if($payment->payment_type === 'deposit_cash')
                        <div class="row mb-2">
                            <div class="col-md-4 text-muted">Deposit Amount</div>
                            <div class="col-md-8">Rs. {{ number_format($payment->deposit_amount, 2) }}</div>
                        </div>
                    @endif

                    <hr>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Platform Commission</div>
                        <div class="col-md-8">Rs. {{ number_format($payment->platform_commission, 2) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Your Net Amount</div>
                        <div class="col-md-8 fw-semibold">
                            @if($isRefunded)
                                Blocked due to refund
                            @else
                                Rs. {{ number_format($payment->vendor_amount, 2) }}
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Payout Status</div>
                        <div class="col-md-8">{{ ucfirst($payment->payout_status ?? 'N/A') }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Settlement Status</div>
                        <div class="col-md-8">{{ ucfirst(str_replace('_', ' ', $payment->settlement_status ?? 'N/A')) }}</div>
                    </div>

                    <hr>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Refund Status</div>
                        <div class="col-md-8">{{ ucfirst($payment->refund_status ?? 'none') }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Refund Amount</div>
                        <div class="col-md-8">Rs. {{ number_format($payment->refund_amount ?? 0, 2) }}</div>
                    </div>

                    @if($payment->refund_processed_at)
                        <div class="row mb-2">
                            <div class="col-md-4 text-muted">Refund Processed At</div>
                            <div class="col-md-8">{{ $payment->refund_processed_at->format('Y-m-d h:i A') }}</div>
                        </div>
                    @endif

                    @if($payment->refund_note)
                        <div class="row mb-2">
                            <div class="col-md-4 text-muted">Refund Note</div>
                            <div class="col-md-8">{{ $payment->refund_note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Vehicle / Booking Info</h5>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Vehicle</div>
                        <div class="col-md-8">
                            {{ $payment->booking->vehicle->brand ?? '' }}
                            {{ $payment->booking->vehicle->model ?? '' }}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Booking Status</div>
                        <div class="col-md-8">{{ ucfirst($payment->booking->status ?? 'N/A') }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Booking Payment Status</div>
                        <div class="col-md-8">{{ ucfirst($payment->booking->payment_status ?? 'N/A') }}</div>
                    </div>

                    @if($loyaltyDiscount > 0)
                        <div class="alert alert-info mt-3 mb-0">
                            This booking used loyalty redemption. The discount is funded by the platform.
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Payout Note</h6>
                    <p class="text-muted mb-0">
                        Loyalty discounts are platform-funded. Refunded bookings do not remain payable to the vendor.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
