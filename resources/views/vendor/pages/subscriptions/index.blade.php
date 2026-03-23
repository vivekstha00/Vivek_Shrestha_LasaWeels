@extends('vendor.layouts.master')

@section('vendor-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Subscription Plans</h2>
        <p class="text-muted mb-0">Choose a monthly or yearly plan to unlock more vehicles and drivers.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(isset($subscriptionSummary))
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Current Plan: {{ $subscriptionSummary['plan_name'] ?? 'Free Plan' }}</h5>
                    <p class="text-muted mb-0">
                        Status: <strong class="text-capitalize">{{ $subscriptionSummary['status'] ?? 'free' }}</strong>
                    </p>

                    @if(!empty($subscriptionSummary['ends_at']))
                        <p class="text-muted mb-0">
                            Expires On: {{ \Carbon\Carbon::parse($subscriptionSummary['ends_at'])->format('Y-m-d h:i A') }}
                        </p>
                    @endif
                </div>

                <div class="text-md-end">
                    <div><strong>Vehicles:</strong> {{ $subscriptionSummary['vehicle_count'] ?? 0 }} / {{ $subscriptionSummary['vehicle_limit'] ?? 2 }}</div>
                    <div><strong>Drivers:</strong> {{ $subscriptionSummary['driver_count'] ?? 0 }} / {{ $subscriptionSummary['driver_limit'] ?? 2 }}</div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    @forelse($plans as $plan)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="fw-bold mb-1">{{ $plan->name }}</h4>
                            <p class="text-muted mb-0 text-capitalize">{{ $plan->billing_cycle }} Plan</p>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>

                    <h3 class="fw-bold mb-3">NPR {{ number_format($plan->price, 2) }}</h3>

                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">✔ Up to {{ $plan->max_vehicles }} vehicles</li>
                        <li class="mb-2">✔ Up to {{ $plan->max_drivers }} drivers</li>
                        @if($plan->description)
                            <li class="text-muted">{{ $plan->description }}</li>
                        @endif
                    </ul>

                    <form action="{{ route('vendor.subscriptions.pay', $plan) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            Pay with Khalti
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info rounded-3 mb-0">
                No paid subscription plans are available right now.
            </div>
        </div>
    @endforelse
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Subscription Payment History</h5>

        @if($subscriptionPayments->count())
            <div class="table-responsive">
                <table class="table align-middle">
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
                                <td>{{ $payment->plan->name ?? 'N/A' }}</td>
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
                                    {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td>{{ $payment->gateway_reference ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $subscriptionPayments->links() }}
            </div>
        @else
            <p class="text-muted mb-0">No subscription payments found yet.</p>
        @endif
    </div>
</div>
@endsection
