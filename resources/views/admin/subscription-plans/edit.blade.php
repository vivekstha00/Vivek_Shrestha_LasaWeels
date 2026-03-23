@extends('admin.layouts.master')

@section('admin-content')
<div class="mb-3">
    <h2 class="fw-bold mb-1">Edit Subscription Plan</h2>
    <p class="text-muted mb-0">Update vendor subscription plan details</p>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.subscription-plans.update', $subscriptionPlan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Plan Name</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $subscriptionPlan->name) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Slug</label>
                    <input type="text" name="slug" class="form-control"
                           value="{{ old('slug', $subscriptionPlan->slug) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Billing Cycle</label>
                    <select name="billing_cycle" class="form-select" required>
                        <option value="free" {{ old('billing_cycle', $subscriptionPlan->billing_cycle) === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="monthly" {{ old('billing_cycle', $subscriptionPlan->billing_cycle) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ old('billing_cycle', $subscriptionPlan->billing_cycle) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Price</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control"
                           value="{{ old('price', $subscriptionPlan->price) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Max Vehicles</label>
                    <input type="number" min="1" name="max_vehicles" class="form-control"
                           value="{{ old('max_vehicles', $subscriptionPlan->max_vehicles) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Max Drivers</label>
                    <input type="number" min="1" name="max_drivers" class="form-control"
                           value="{{ old('max_drivers', $subscriptionPlan->max_drivers) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $subscriptionPlan->description) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                               {{ old('is_active', $subscriptionPlan->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    Update Plan
                </button>
                <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
