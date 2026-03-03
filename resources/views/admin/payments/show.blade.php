@extends('admin.layouts.master')

@section('title', 'Payment Details')

@section('admin-content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Payment #{{ $payment->id }}</h3>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Payment Info</h5>

                    <p class="mb-1"><strong>Method:</strong> {{ strtoupper($payment->method) }}</p>
                    <p class="mb-1"><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>
                    <p class="mb-1"><strong>Amount:</strong> Rs. {{ number_format($payment->amount, 2) }}</p>

                    @if(!empty($payment->gateway_reference))
                        <p class="mb-1"><strong>Gateway Ref:</strong> {{ $payment->gateway_reference }}</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body">
                    <h5 class="mb-3">Booking Info</h5>

                    <p class="mb-1"><strong>Booking ID:</strong> #{{ $payment->booking->id ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Booking Status:</strong> {{ ucfirst($payment->booking->status ?? 'N/A') }}</p>
                    <p class="mb-1"><strong>Payment Status:</strong> {{ ucfirst($payment->booking->payment_status ?? 'N/A') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Update Payment</h5>

                    <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ $payment->status==='pending'?'selected':'' }}>Pending</option>
                                <option value="completed" {{ $payment->status==='completed'?'selected':'' }}>Completed</option>
                                <option value="failed" {{ $payment->status==='failed'?'selected':'' }}>Failed</option>
                                <option value="refunded" {{ $payment->status==='refunded'?'selected':'' }}>Refunded</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payout Status (Optional)</label>
                            <select name="payout_status" class="form-select">
                                <option value="">Keep same</option>
                                <option value="unpaid" {{ $payment->payout_status==='unpaid'?'selected':'' }}>Unpaid</option>
                                <option value="pending" {{ $payment->payout_status==='pending'?'selected':'' }}>Pending</option>
                                <option value="paid" {{ $payment->payout_status==='paid'?'selected':'' }}>Paid</option>
                            </select>
                        </div>

                        <button class="btn btn-primary w-100">Update</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body">
                    <h6 class="mb-2">User</h6>
                    <p class="mb-1">{{ $payment->user->name ?? 'N/A' }}</p>
                    <p class="mb-0 text-muted">{{ $payment->user->email ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
