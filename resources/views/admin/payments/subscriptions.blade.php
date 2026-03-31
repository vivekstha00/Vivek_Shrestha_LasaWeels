@extends('admin.layouts.master')

@section('title', 'Subscription Payment Records')

@section('admin-content')
<div class="mb-4">
    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Payments
    </a>
    <h2 class="fw-bold mb-1">Vendor Subscription Payments</h2>
    <p class="text-muted mb-0">All completed vendor subscription payments till now</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="small text-muted">Total Subscription Revenue</div>
                <div class="fw-bold fs-4 text-success">Rs. {{ number_format($totalSubscriptionRevenue ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="small text-muted">Total Subscription Transactions</div>
                <div class="fw-bold fs-4 text-dark">{{ $subscriptionTransactionsCount ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.payments.subscriptions') }}" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Search by id, purchase order, gateway ref, vendor...">
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
                    <th>Vendor</th>
                    <th>Plan</th>
                    <th>Gateway</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Paid At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptionPayments as $payment)
                    <tr>
                        <td class="px-4 fw-semibold">#{{ $payment->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $payment->vendor->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $payment->vendor->email ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $payment->plan->name ?? 'N/A' }}</td>
                        <td class="text-uppercase">{{ $payment->payment_gateway ?? 'N/A' }}</td>
                        <td class="fw-semibold text-success">Rs. {{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span class="badge text-bg-success">{{ ucfirst($payment->status) }}</span>
                        </td>
                        <td>{{ ($payment->paid_at ?? $payment->created_at)?->format('d M Y, h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No subscription payment records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $subscriptionPayments->links() }}
    </div>
</div>
@endsection
