@extends('admin.layouts.master')

@section('title', 'Payment Details')

@section('admin-content')
<div class="mb-4">
    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Payments
    </a>
    <h2 class="fw-bold mb-1">Payment #{{ $payment->id }}</h2>
    <p class="text-muted mb-0">Manage payment and vendor settlement</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@php
    $isRefunded = $payment->refund_status === 'refunded' || ($payment->booking && $payment->booking->status === 'cancelled');
@endphp

@if($isRefunded)
    <div class="alert alert-danger rounded-3 d-flex align-items-center">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        This booking/payment has been refunded. Vendor payout is blocked.
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">

        <!-- Payment Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small">Total Amount</div>
                        <h4 class="fw-bold text-primary mb-0">Rs. {{ number_format($payment->amount, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small">Paid Amount</div>
                        <h4 class="fw-bold text-success mb-0">Rs. {{ number_format($payment->paid_amount, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small">Remaining</div>
                        <h4 class="fw-bold text-danger mb-0">Rs. {{ number_format($payment->remaining_amount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-credit-card me-2 text-primary"></i> Payment Details
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Payment Type</div>
                        <div class="fw-semibold">
                            {{ $payment->payment_type === 'deposit_cash' ? 'Deposit + Cash' : 'Full Online' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Method</div>
                        <div class="fw-semibold">{{ strtoupper($payment->method) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Status</div>
                        <div>
                            @php
                                $payStatusClass = match($payment->status) {
                                    'completed' => 'text-bg-success',
                                    'failed' => 'text-bg-danger',
                                    'refunded' => 'text-bg-dark',
                                    default => 'text-bg-warning',
                                };
                            @endphp
                            <span class="badge {{ $payStatusClass }} rounded-pill px-3 py-2">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>

                    @if($payment->payment_type === 'deposit_cash')
                        <div class="col-md-6">
                            <div class="text-muted small">Deposit Amount</div>
                            <div class="fw-semibold">Rs. {{ number_format($payment->deposit_amount, 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Deposit Status</div>
                            <div class="fw-semibold">{{ ucfirst($payment->deposit_status ?? 'N/A') }}</div>
                        </div>
                    @endif

                    @if($payment->gateway_reference)
                        <div class="col-md-6">
                            <div class="text-muted small">Gateway Reference</div>
                            <div class="fw-semibold font-monospace small">{{ $payment->gateway_reference }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Settlement & Payout -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-building-columns me-2 text-primary"></i> Settlement & Payout
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Platform Commission</div>
                        <div class="fw-bold text-primary">Rs. {{ number_format($payment->platform_commission, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Vendor Net Amount</div>
                        <div class="fw-bold text-success">Rs. {{ number_format($payment->vendor_amount, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Payout Status</div>
                        <div class="fw-semibold">{{ ucfirst($payment->payout_status ?? 'N/A') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Settlement Status</div>
                        <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $payment->settlement_status ?? 'N/A')) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Info -->
        @if($payment->refund_status && $payment->refund_status !== 'none')
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fa-solid fa-rotate-left me-2 text-danger"></i> Refund Information
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Refund Status</div>
                            <div class="fw-semibold">{{ ucfirst($payment->refund_status) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Refund Amount</div>
                            <div class="fw-bold text-danger">Rs. {{ number_format($payment->refund_amount ?? 0, 2) }}</div>
                        </div>
                        @if($payment->refund_requested_at)
                            <div class="col-md-6">
                                <div class="text-muted small">Requested At</div>
                                <div class="fw-semibold">{{ $payment->refund_requested_at->format('d M Y, h:i A') }}</div>
                            </div>
                        @endif
                        @if($payment->refund_processed_at)
                            <div class="col-md-6">
                                <div class="text-muted small">Processed At</div>
                                <div class="fw-semibold">{{ $payment->refund_processed_at->format('d M Y, h:i A') }}</div>
                            </div>
                        @endif
                        @if($payment->refund_note)
                            <div class="col-12">
                                <div class="text-muted small">Refund Note</div>
                                <div class="alert alert-warning mb-0 mt-1 small">{{ $payment->refund_note }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Booking Details -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-calendar-check me-2 text-primary"></i> Booking Details
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Booking ID</div>
                        <div class="fw-semibold">#{{ $payment->booking->id ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Booking Status</div>
                        <div class="fw-semibold text-capitalize">{{ $payment->booking->status ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Payment Status</div>
                        <div class="fw-semibold text-capitalize">{{ $payment->booking->payment_status ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Vehicle</div>
                        <div class="fw-semibold">
                            {{ $payment->booking->vehicle->brand ?? '' }}
                            {{ $payment->booking->vehicle->model ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">

        <!-- Update Form -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Update Payment
                </h5>

                <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payment Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ $payment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ $payment->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payout Status</label>
                        <select name="payout_status" class="form-select">
                            <option value="unpaid" {{ $payment->payout_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="pending" {{ $payment->payout_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $payment->payout_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="hold" {{ $payment->payout_status === 'hold' ? 'selected' : '' }}>Hold</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Settlement Status</label>
                        <select name="settlement_status" class="form-select">
                            <option value="pending_balance" {{ $payment->settlement_status === 'pending_balance' ? 'selected' : '' }}>Pending Balance</option>
                            <option value="balance_received" {{ $payment->settlement_status === 'balance_received' ? 'selected' : '' }}>Balance Received</option>
                            <option value="payout_pending" {{ $payment->settlement_status === 'payout_pending' ? 'selected' : '' }}>Payout Pending</option>
                            <option value="paid_to_vendor" {{ $payment->settlement_status === 'paid_to_vendor' ? 'selected' : '' }}>Paid To Vendor</option>
                            <option value="not_applicable" {{ $payment->settlement_status === 'not_applicable' ? 'selected' : '' }}>Not Applicable</option>
                            <option value="refunded" {{ $payment->settlement_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    @if($payment->payment_type === 'deposit_cash')
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Deposit Status</label>
                            <select name="deposit_status" class="form-select">
                                <option value="pending" {{ $payment->deposit_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $payment->deposit_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="refunded" {{ $payment->deposit_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                <option value="forfeited" {{ $payment->deposit_status === 'forfeited' ? 'selected' : '' }}>Forfeited</option>
                            </select>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-check me-1"></i> Update
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-2">
                    <i class="fa-solid fa-user me-1 text-primary"></i> Customer
                </h6>
                <div class="fw-semibold">{{ $payment->user->name ?? 'N/A' }}</div>
                <div class="text-muted small">{{ $payment->user->email ?? '' }}</div>
            </div>
        </div>

        <!-- Vendor Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-2">
                    <i class="fa-solid fa-building me-1 text-primary"></i> Vendor
                </h6>
                <div class="fw-semibold">{{ $payment->vendor->name ?? 'N/A' }}</div>
                <div class="text-muted small">{{ $payment->vendor->email ?? '' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
