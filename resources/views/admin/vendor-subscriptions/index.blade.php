@extends('admin.layouts.master')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="fw-bold mb-1">Vendor Subscriptions</h2>
        <p class="text-muted mb-0">Manage assigned subscription plans for vendors</p>
    </div>

    <a href="{{ route('admin.vendor-subscriptions.create') }}" class="btn btn-primary rounded-pill px-4">
        + Assign Subscription
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">
        {{ session('success') }}
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if($vendorSubscriptions->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Plan</th>
                            <th>Billing</th>
                            <th>Amount Paid</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendorSubscriptions as $subscription)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $subscription->vendor->name ?? 'N/A' }}</div>
                                    @if(!empty($subscription->vendor->email))
                                        <small class="text-muted">{{ $subscription->vendor->email }}</small>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $subscription->plan->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $subscription->plan->slug ?? '' }}</small>
                                </td>

                                <td class="text-capitalize">
                                    {{ $subscription->plan->billing_cycle ?? 'N/A' }}
                                </td>

                                <td>NPR {{ number_format($subscription->amount_paid, 2) }}</td>

                                <td>
                                    {{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d h:i A') : '-' }}
                                </td>

                                <td>
                                    {{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d h:i A') : 'No expiry' }}
                                </td>

                                <td>
                                    @if($subscription->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($subscription->status === 'expired')
                                        <span class="badge bg-warning text-dark">Expired</span>
                                    @else
                                        <span class="badge bg-secondary">Cancelled</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.vendor-subscriptions.edit', $subscription) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $vendorSubscriptions->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No vendor subscriptions found</h5>
                <p class="text-muted mb-3">Assign a subscription plan to a vendor.</p>
                <a href="{{ route('admin.vendor-subscriptions.create') }}" class="btn btn-primary rounded-pill px-4">
                    Assign Subscription
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
