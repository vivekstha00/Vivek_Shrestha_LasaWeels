@extends('vendor.layouts.master')

@section('title', 'Subscription Plans')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Subscription Plans</h2>
    <p class="text-muted mb-0">Choose a monthly or yearly plan to unlock more vehicles and drivers.</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Current Plan Summary -->
@if(isset($subscriptionSummary))
    <div class="card subscription-box mb-5">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Current Plan: {{ $subscriptionSummary['plan_name'] ?? 'Free Plan' }}</h5>
                    <p class="mb-1">
                        Status: <strong class="text-success text-capitalize">{{ $subscriptionSummary['status'] ?? 'free' }}</strong>
                    </p>
                    @if(!empty($subscriptionSummary['ends_at']))
                        <p class="text-muted mb-0">
                            Expires On: {{ \Carbon\Carbon::parse($subscriptionSummary['ends_at'])->format('d M Y, h:i A') }}
                        </p>
                    @endif
                </div>

                <div class="text-end">
                    <div><strong>Vehicles:</strong> {{ $subscriptionSummary['vehicle_count'] ?? 0 }} / {{ $subscriptionSummary['vehicle_limit'] ?? 10 }}</div>
                    <div><strong>Drivers:</strong> {{ $subscriptionSummary['driver_count'] ?? 0 }} / {{ $subscriptionSummary['driver_limit'] ?? 8 }}</div>
                </div>
            </div>

            <div class="alert alert-light border mt-3 mb-0">
                Renewing the same plan extends your current expiry date. Switching to a different plan activates the new plan immediately.
            </div>
        </div>
    </div>
@endif

<!-- Subscription Plans -->
<div class="row g-4 mb-5">
    @forelse($plans as $plan)
        @php
            $isCurrentPlan = isset($currentActivePlanId) && $currentActivePlanId == $plan->id;
        @endphp

        <div class="col-md-6">
            <div class="card h-100 {{ $isCurrentPlan ? 'border-primary' : '' }}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">{{ $plan->name }}</h4>
                            <p class="text-muted mb-0 text-capitalize">{{ $plan->billing_cycle }} Plan</p>
                        </div>

                        @if($isCurrentPlan)
                            <span class="badge bg-primary px-3 py-2">Current Plan</span>
                        @else
                            <span class="badge bg-success px-3 py-2">Available</span>
                        @endif
                    </div>

                    <h3 class="fw-bold mb-4">NPR {{ number_format($plan->price, 2) }}</h3>

                    <ul class="list-unstyled mb-5">
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-check text-success"></i>
                            Up to {{ $plan->max_vehicles }} vehicles
                        </li>
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-check text-success"></i>
                            Up to {{ $plan->max_drivers }} drivers
                        </li>
                        @if($plan->description)
                            <li class="text-muted">{{ $plan->description }}</li>
                        @endif
                    </ul>

                    <form action="{{ route('vendor.subscriptions.pay', $plan) }}" method="POST">
                        @csrf
                        @if($isCurrentPlan)
                            <button type="submit" class="btn btn-outline-primary w-100">
                                Renew with Khalti
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary w-100">
                                Pay with Khalti
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-4">
                No paid subscription plans are available right now.
            </div>
        </div>
    @endforelse
</div>

<!-- Subscription Payment History -->
<div class="card">
    <div class="card-body">
        <h5 class="fw-bold mb-4">Subscription Payment History</h5>

        @if($subscriptionPayments->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Paid At</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptionPayments as $payment)
                            <tr>
                                <td class="fw-medium">{{ $payment->plan->name ?? 'N/A' }}</td>
                                <td>NPR {{ number_format($payment->amount, 2) }}</td>
                                <td class="text-capitalize">{{ $payment->payment_gateway }}</td>
                                <td>
                                    @if($payment->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $payment->paid_at ? $payment->paid_at->format('d M Y, h:i A') : '—' }}
                                </td>
                                <td class="text-muted small">{{ $payment->gateway_reference ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscriptionPayments->links() }}
            </div>
        @else
            <p class="text-muted text-center py-4">No subscription payments found yet.</p>
        @endif
    </div>
</div>
@endsection
