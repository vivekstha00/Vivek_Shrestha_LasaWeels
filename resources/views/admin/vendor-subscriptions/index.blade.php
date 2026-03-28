@extends('admin.layouts.master')

@section('title', 'Vendor Subscriptions')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Vendor Subscriptions</h2>
    <p class="text-muted">Manage vendor subscription plans and status</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Vendor</th>
                    <th>Plan</th>
                    <th>Billing Cycle</th>
                    <th>Amount Paid</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendorSubscriptions as $subscription)
                    <tr onclick="window.location='{{ route('admin.vendor-subscriptions.show', $subscription) }}'" style="cursor:pointer;">
                        <td>
                            <div class="fw-semibold">
                                {{ $subscription->vendor?->company_name ?? $subscription->vendor?->name ?? '—' }}
                            </div>
                            <small class="text-muted">{{ $subscription->vendor?->email ?? '—' }}</small>
                        </td>
                        <td>{{ $subscription->plan?->name ?? '—' }}</td>
                        <td class="text-capitalize">{{ $subscription->plan?->billing_cycle ?? '—' }}</td>
                        <td>NPR {{ number_format((float) ($subscription->amount_paid ?? 0), 2) }}</td>
                        <td>{{ $subscription->starts_at?->format('d M Y') ?? '—' }}</td>
                        <td>{{ $subscription->ends_at?->format('d M Y') ?? '—' }}</td>
                        <td>
                            @php
                                $statusClass = match($subscription->status) {
                                    'active' => 'bg-success',
                                    'expired' => 'bg-secondary',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($subscription->status ?? 'unknown') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.vendor-subscriptions.edit', $subscription) }}"
                               class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No vendor subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $vendorSubscriptions->links() }}
    </div>
</div>
@endsection
