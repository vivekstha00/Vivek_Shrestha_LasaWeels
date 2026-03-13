@extends('vendor.layouts.master')

@section('title', 'Payment Details')

@section('vendor-content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Payment #{{ $payment->id }}</h3>
            <p class="text-muted mb-0">View earning and payout details</p>
        </div>

        <a href="{{ route('vendor.payments.index') }}" class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

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
                        <div class="col-md-4 text-muted">Total Amount</div>
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
                        <div class="col-md-8 fw-semibold">Rs. {{ number_format($payment->vendor_amount, 2) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Payout Status</div>
                        <div class="col-md-8">{{ ucfirst($payment->payout_status ?? 'N/A') }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Settlement Status</div>
                        <div class="col-md-8">{{ ucfirst(str_replace('_', ' ', $payment->settlement_status ?? 'N/A')) }}</div>
                    </div>
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
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Payout Note</h6>
                    <p class="text-muted mb-0">
                        Payout statuses are managed by admin. If you believe a payment or payout record is incorrect,
                        contact admin support.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
