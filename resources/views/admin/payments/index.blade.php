@extends('admin.layouts.master')

@section('title', 'Payments')

@section('admin-content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Payments</h3>
            <p class="text-muted mb-0">Manage customer payments and vendor settlements</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form class="row g-2" method="GET" action="{{ route('admin.payments.index') }}">
                <div class="col-lg-4">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control rounded-3"
                        placeholder="Search payment, booking, user, vendor"
                    >
                </div>

                <div class="col-lg-2 col-md-4">
                    <select name="method" class="form-select rounded-3">
                        <option value="">All Methods</option>
                        <option value="khalti" {{ request('method') == 'khalti' ? 'selected' : '' }}>Khalti</option>
                        <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="manual" {{ request('method') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <select name="payment_type" class="form-select rounded-3">
                        <option value="">All Types</option>
                        <option value="full_online" {{ request('payment_type') == 'full_online' ? 'selected' : '' }}>Full Online</option>
                        <option value="deposit_cash" {{ request('payment_type') == 'deposit_cash' ? 'selected' : '' }}>Deposit + Cash</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <select name="status" class="form-select rounded-3">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <button class="btn btn-primary w-100 rounded-3">Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">#</th>
                        <th>Customer</th>
                        <th>Vendor</th>
                        <th>Booking</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                        <th>Payout</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr
                            class="payment-row"
                            onclick="window.location='{{ route('admin.payments.show', $p->id) }}'"
                            style="cursor:pointer;"
                        >
                            <td class="px-4 fw-semibold">#{{ $p->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $p->user->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $p->user->email ?? '' }}</small>
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $p->vendor->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $p->vendor->email ?? '' }}</small>
                            </td>

                            <td class="fw-medium">#{{ $p->booking_id }}</td>

                            <td>
                                @if($p->payment_type === 'deposit_cash')
                                    <span class="badge text-bg-info rounded-pill px-3 py-2">Deposit + Cash</span>
                                @else
                                    <span class="badge text-bg-primary rounded-pill px-3 py-2">Full Online</span>
                                @endif
                            </td>

                            <td>
                                @if($p->status === 'completed')
                                    <span class="badge text-bg-success rounded-pill px-3 py-2">Completed</span>
                                @elseif($p->status === 'failed')
                                    <span class="badge text-bg-danger rounded-pill px-3 py-2">Failed</span>
                                @elseif($p->status === 'refunded')
                                    <span class="badge text-bg-dark rounded-pill px-3 py-2">Refunded</span>
                                @else
                                    <span class="badge text-bg-warning rounded-pill px-3 py-2">Pending</span>
                                @endif
                            </td>

                            <td>Rs. {{ number_format($p->amount, 2) }}</td>
                            <td>Rs. {{ number_format($p->paid_amount, 2) }}</td>
                            <td>Rs. {{ number_format($p->remaining_amount, 2) }}</td>

                            <td>
                                @if($p->payout_status === 'paid')
                                    <span class="badge text-bg-success rounded-pill px-3 py-2">Paid</span>
                                @elseif($p->payout_status === 'pending')
                                    <span class="badge text-bg-warning rounded-pill px-3 py-2">Pending</span>
                                @else
                                    <span class="badge text-bg-secondary rounded-pill px-3 py-2">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                No payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>
</div>

<style>
    .payment-row:hover {
        background-color: #f8f9fa;
        transition: 0.2s ease;
    }
</style>
@endsection
