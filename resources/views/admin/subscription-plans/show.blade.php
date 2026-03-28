@extends('admin.layouts.master')

@section('title', 'Plan Details')

@section('admin-content')
<div class="mb-4">
    <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Plans
    </a>
    <h2 class="fw-bold mb-1">{{ $subscriptionPlan->name }}</h2>
    <p class="text-muted mb-0">Subscription plan details and statistics</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Price</div>
                <h3 class="fw-bold text-primary mb-0">NPR {{ number_format($subscriptionPlan->price, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Billing Cycle</div>
                <h4 class="fw-bold text-capitalize mb-0">{{ $subscriptionPlan->billing_cycle }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Active Subscribers</div>
                <h3 class="fw-bold mb-0">{{ $activeSubscribers }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body text-center">
                @if($subscriptionPlan->is_active)
                    <h5 class="fw-bold text-success mb-0">Active</h5>
                @else
                    <h5 class="fw-bold text-secondary mb-0">Inactive</h5>
                @endif
                <div class="text-muted small mt-1">Current Status</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">
            <i class="fa-solid fa-list me-2 text-primary"></i> Plan Information
        </h5>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Plan Name</div>
                <div class="fw-semibold">{{ $subscriptionPlan->name }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Slug</div>
                <div><span class="badge bg-dark">{{ $subscriptionPlan->slug }}</span></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Vehicle Limit</div>
                <div class="fw-semibold">{{ $subscriptionPlan->max_vehicles }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Driver Limit</div>
                <div class="fw-semibold">{{ $subscriptionPlan->max_drivers }}</div>
            </div>
        </div>

        @if($subscriptionPlan->description)
            <hr class="my-3">
            <div class="text-muted small">Description</div>
            <div class="fw-semibold">{{ $subscriptionPlan->description }}</div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body d-flex justify-content-end">
        <a href="{{ route('admin.subscription-plans.edit', $subscriptionPlan) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen me-1"></i> Edit Plan
        </a>
    </div>
</div>
@endsection
