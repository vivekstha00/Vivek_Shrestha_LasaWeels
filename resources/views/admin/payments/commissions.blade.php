@extends('admin.layouts.master')

@section('title', 'Commission Records')

@section('admin-content')
<div class="mb-4">
    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Payments
    </a>
    <h2 class="fw-bold mb-1">Commission Records</h2>
    <p class="text-muted mb-0">All completed booking commission earnings till now</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="small text-muted">Total Commission Earned</div>
                <div class="fw-bold fs-4 text-primary">Rs. {{ number_format($totalCommission ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="small text-muted">Total Commission Transactions</div>
                <div class="fw-bold fs-4 text-dark">{{ $commissionTransactionsCount ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.payments.commissions') }}" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Search by payment id, booking id, customer, vendor...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="px-4">Payment</th>
                    <th>Booking</th>
                    <th>Customer / Vendor</th>
                    <th>Payment Total</th>
                    <th>Commission</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissionPayments as $payment)
                    @php
                        $isRefunded = $payment->refund_status === 'refunded' || ($payment->booking && $payment->booking->status === 'cancelled');
                    @endphp
                    <tr>
                        <td class="px-4 fw-semibold">
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-decoration-none">
                                #{{ $payment->id }}
                            </a>
                        </td>
                        <td>#{{ $payment->booking_id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $payment->user->name ?? 'N/A' }}</div>
                            <small class="text-muted">→ {{ $payment->vendor->name ?? 'N/A' }}</small>
                        </td>
                        <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                        <td class="fw-semibold text-primary">Rs. {{ number_format($payment->platform_commission, 2) }}</td>
                        <td>
                            @if($isRefunded)
                                <span class="badge text-bg-dark">Refunded</span>
                            @else
                                <span class="badge text-bg-success">Completed</span>
                            @endif
                        </td>
                        <td>{{ $payment->created_at?->format('d M Y, h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No commission records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $commissionPayments->links() }}
    </div>
</div>
@endsection
