@extends('admin.layouts.master')

@section('title', 'Vendor Subscription Details')

@section('admin-content')
<div class="mb-4">
    <a href="{{ route('admin.vendor-subscriptions.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Subscriptions
    </a>
    <h2 class="fw-bold mb-1">Subscription Details</h2>
    <p class="text-muted mb-0">Vendor subscription information and status</p>
</div>

@php
    $statusConfig = match($vendorSubscription->status) {
        'active' => ['label' => 'Active', 'color' => 'text-success'],
        'expired' => ['label' => 'Expired', 'color' => 'text-secondary'],
        'cancelled' => ['label' => 'Cancelled', 'color' => 'text-danger'],
        default => ['label' => ucfirst($vendorSubscription->status), 'color' => 'text-warning'],
    };
    $daysRemaining = $vendorSubscription->ends_at ? now()->diffInDays($vendorSubscription->ends_at, false) : null;
@endphp

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <h5 class="fw-bold {{ $statusConfig['color'] }} mb-0">{{ $statusConfig['label'] }}</h5>
                <div class="text-muted small mt-1">Current Status</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Amount Paid</div>
                <h3 class="fw-bold text-primary mb-0">NPR {{ number_format((float) ($vendorSubscription->amount_paid ?? 0), 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Start Date</div>
                <h5 class="fw-bold mb-0">{{ $vendorSubscription->starts_at?->format('d M Y') ?? '—' }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">End Date</div>
                <h5 class="fw-bold mb-0">{{ $vendorSubscription->ends_at?->format('d M Y') ?? '—' }}</h5>
                @if(!is_null($daysRemaining) && $vendorSubscription->status === 'active')
                    <div class="text-muted small mt-1">{{ $daysRemaining > 0 ? $daysRemaining . ' days remaining' : 'Expiring soon' }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">
            <i class="fa-solid fa-user-shield me-2 text-primary"></i> Subscription Information
        </h5>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Vendor</div>
                <div class="fw-semibold">{{ $vendorSubscription->vendor?->company_name ?? $vendorSubscription->vendor?->name ?? '—' }}</div>
                <small class="text-muted">{{ $vendorSubscription->vendor?->email ?? '' }}</small>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Plan</div>
                <div class="fw-semibold">{{ $vendorSubscription->plan?->name ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Billing Cycle</div>
                <div class="fw-semibold text-capitalize">{{ $vendorSubscription->plan?->billing_cycle ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Plan Limits</div>
                <div class="fw-semibold">{{ $vendorSubscription->plan?->max_vehicles ?? '—' }} vehicles / {{ $vendorSubscription->plan?->max_drivers ?? '—' }} drivers</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body d-flex justify-content-end">
        <a href="{{ route('admin.vendor-subscriptions.edit', $vendorSubscription) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen me-1"></i> Edit Subscription
        </a>
    </div>
</div>
@endsection
