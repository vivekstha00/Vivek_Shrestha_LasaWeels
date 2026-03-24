@extends('admin.layouts.master')

@section('title', 'Payment Details')

@section('admin-content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Payment #{{ $payment->id }}</h3>
            <p class="text-muted mb-0">Manage payment and vendor settlement</p>
        </div>

        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $isRefunded = $payment->refund_status === 'refunded' || ($payment->booking && $payment->booking->status === 'cancelled');
    @endphp

    @if($isRefunded)
        <div class="alert alert-danger rounded-3">
            This booking/payment has been refunded. Vendor payout is blocked.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Payment Details</h5>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Payment Type</div>
                        <div class="col-md-8">
                            {{ $payment->payment_type === 'deposit_cash' ? 'Deposit + Cash' : 'Full Online' }}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Method</div>
                        <div class="col-md-8">{{ strtoupper($payment->method) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Status</div>
                        <div class="col-md-8">{{ ucfirst($payment->status) }}</div>
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

                        <div class="row mb-2">
                            <div class="col-md-4 text-muted">Deposit Status</div>
                            <div class="col-md-8">{{ ucfirst($payment->deposit_status ?? 'N/A') }}</div>
                        </div>
                    @endif

                    <hr>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Platform Commission</div>
                        <div class="col-md-8">Rs. {{ number_format($payment->platform_commission, 2) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Vendor Net Amount</div>
                        <div class="col-md-8">Rs. {{ number_format($payment->vendor_amount, 2) }}</div>
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

                    @if($payment->refund_requested_at)
                        <div class="row mb-2">
                            <div class="col-md-4 text-muted">Refund Requested At</div>
                            <div class="col-md-8">{{ $payment->refund_requested_at->format('Y-m-d h:i A') }}</div>
                        </div>
                    @endif

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

                    @if($payment->gateway_reference)
                        <div class="row mb-2">
                            <div class="col-md-4 text-muted">Gateway Reference</div>
                            <div class="col-md-8">{{ $payment->gateway_reference }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Booking Details</h5>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Booking ID</div>
                        <div class="col-md-8">#{{ $payment->booking->id ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Booking Status</div>
                        <div class="col-md-8">{{ ucfirst($payment->booking->status ?? 'N/A') }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Payment Status</div>
                        <div class="col-md-8">{{ ucfirst($payment->booking->payment_status ?? 'N/A') }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Vehicle</div>
                        <div class="col-md-8">
                            {{ $payment->booking->vehicle->brand ?? '' }}
                            {{ $payment->booking->vehicle->model ?? '' }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Update Payment</h5>

                    <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Payment Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $payment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $payment->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payout Status</label>
                            <select name="payout_status" class="form-select">
                                <option value="unpaid" {{ $payment->payout_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="pending" {{ $payment->payout_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $payment->payout_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="hold" {{ $payment->payout_status === 'hold' ? 'selected' : '' }}>Hold</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Settlement Status</label>
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
                                <label class="form-label">Deposit Status</label>
                                <select name="deposit_status" class="form-select">
                                    <option value="pending" {{ $payment->deposit_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $payment->deposit_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="refunded" {{ $payment->deposit_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    <option value="forfeited" {{ $payment->deposit_status === 'forfeited' ? 'selected' : '' }}>Forfeited</option>
                                </select>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary w-100">Update</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2">Customer</h6>
                    <p class="mb-1">{{ $payment->user->name ?? 'N/A' }}</p>
                    <p class="text-muted mb-0">{{ $payment->user->email ?? '' }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2">Vendor</h6>
                    <p class="mb-1">{{ $payment->vendor->name ?? 'N/A' }}</p>
                    <p class="text-muted mb-0">{{ $payment->vendor->email ?? '' }}</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
