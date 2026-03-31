@extends('admin.layouts.master')

@section('title', 'Payments')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Payments</h2>
    <p class="text-muted">Manage customer payments and vendor settlements</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="small text-muted">Commission Revenue (Bookings)</div>
                <div class="fw-bold fs-4 text-primary">Rs. {{ number_format($commissionRevenue ?? 0, 2) }}</div>
                <div class="small text-muted mt-1">{{ $successfulCommissionPayments ?? 0 }} successful booking payments</div>
                <a href="{{ route('admin.payments.commissions') }}" class="btn btn-sm btn-outline-primary mt-3">
                    View Commission Records
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="small text-muted">Subscription Revenue (Vendors)</div>
                <div class="fw-bold fs-4 text-success">Rs. {{ number_format($subscriptionRevenue ?? 0, 2) }}</div>
                <div class="small text-muted mt-1">{{ $successfulSubscriptionPayments ?? 0 }} completed subscription payments</div>
                <a href="{{ route('admin.payments.subscriptions') }}" class="btn btn-sm btn-outline-success mt-3">
                    View Subscription Records
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-light">
            <div class="card-body">
                <div class="small text-muted">Total Platform Revenue</div>
                <div class="fw-bold fs-4 text-dark">Rs. {{ number_format($totalPlatformRevenue ?? 0, 2) }}</div>
                <div class="small text-muted mt-1">Commission + Subscription</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
            <div class="col-md-6 col-lg-3">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Search payment, booking, user...">
            </div>
            <div class="col-md-6 col-lg-2">
                <select name="method" class="form-select">
                    <option value="">All Methods</option>
                    <option value="khalti" {{ request('method') == 'khalti' ? 'selected' : '' }}>Khalti</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="manual" {{ request('method') == 'manual' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <select name="payment_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="full_online" {{ request('payment_type') == 'full_online' ? 'selected' : '' }}>Full Online</option>
                    <option value="deposit_cash" {{ request('payment_type') == 'deposit_cash' ? 'selected' : '' }}>Deposit + Cash</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-3">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-clickable align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="px-4">#</th>
                    <th>Customer / Vendor</th>
                    <th>Booking</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Payout</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    @php
                        $isRefunded = $p->refund_status === 'refunded' || ($p->booking && $p->booking->status === 'cancelled');
                    @endphp

                    <tr class="{{ $isRefunded ? 'table-danger' : '' }}"
                        onclick="window.location='{{ route('admin.payments.show', $p->id) }}'">

                        <td class="px-4 fw-semibold">#{{ $p->id }}</td>

                        <td>
                            <div class="fw-semibold">{{ $p->user->name ?? 'N/A' }}</div>
                            <small class="text-muted">→ {{ $p->vendor->name ?? 'N/A' }}</small>
                        </td>

                        <td>
                            <div class="fw-medium">#{{ $p->booking_id }}</div>
                            <small class="text-muted text-capitalize">{{ $p->booking->status ?? 'N/A' }}</small>
                        </td>

                        <td>
                            @if($p->payment_type === 'deposit_cash')
                                <span class="badge text-bg-info rounded-pill px-2 py-1">Deposit+Cash</span>
                            @else
                                <span class="badge text-bg-primary rounded-pill px-2 py-1">Full Online</span>
                            @endif
                        </td>

                        <td>
                            @if($p->status === 'refunded')
                                <span class="badge text-bg-dark rounded-pill px-2 py-1">Refunded</span>
                            @elseif($p->status === 'completed')
                                <span class="badge text-bg-success rounded-pill px-2 py-1">Completed</span>
                            @elseif($p->status === 'failed')
                                <span class="badge text-bg-danger rounded-pill px-2 py-1">Failed</span>
                            @else
                                <span class="badge text-bg-warning rounded-pill px-2 py-1">Pending</span>
                            @endif
                        </td>

                        <td class="fw-semibold">Rs. {{ number_format($p->amount, 2) }}</td>
                        <td>Rs. {{ number_format($p->paid_amount, 2) }}</td>

                        <td>
                            @if($isRefunded || $p->payout_status === 'hold')
                                <span class="badge text-bg-dark rounded-pill px-2 py-1">Hold</span>
                            @elseif($p->payout_status === 'paid')
                                <span class="badge text-bg-success rounded-pill px-2 py-1">Paid</span>
                            @elseif($p->payout_status === 'pending')
                                <span class="badge text-bg-warning rounded-pill px-2 py-1">Pending</span>
                            @else
                                <span class="badge text-bg-secondary rounded-pill px-2 py-1">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            No payments found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $payments->links() }}
    </div>
</div>
@endsection
