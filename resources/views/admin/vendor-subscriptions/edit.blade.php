@extends('admin.layouts.master')

@section('admin-content')
<div class="mb-3">
    <h2 class="fw-bold mb-1">Edit Vendor Subscription</h2>
    <p class="text-muted mb-0">Update assigned subscription details</p>
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
        <form action="{{ route('admin.vendor-subscriptions.update', $vendorSubscription) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Vendor</label>
                    <select name="vendor_id" class="form-select" required>
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->user_id }}"
                                {{ old('vendor_id', $vendorSubscription->vendor_id) == $vendor->user_id ? 'selected' : '' }}>
                                {{ $vendor->company_name }} - {{ $vendor->user->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Subscription Plan</label>
                    <select name="subscription_plan_id" class="form-select" required>
                        <option value="">Select Plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}"
                                {{ old('subscription_plan_id', $vendorSubscription->subscription_plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} ({{ ucfirst($plan->billing_cycle) }}) - NPR {{ number_format($plan->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="datetime-local" name="starts_at" class="form-control"
                           value="{{ old('starts_at', optional($vendorSubscription->starts_at)->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="datetime-local" name="ends_at" class="form-control"
                           value="{{ old('ends_at', optional($vendorSubscription->ends_at)->format('Y-m-d\TH:i')) }}">
                    <small class="text-muted">Optional. Leave blank to auto-calculate from plan cycle.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Amount Paid</label>
                    <input type="number" step="0.01" min="0" name="amount_paid" class="form-control"
                           value="{{ old('amount_paid', $vendorSubscription->amount_paid) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ old('status', $vendorSubscription->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ old('status', $vendorSubscription->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ old('status', $vendorSubscription->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    Update Subscription
                </button>
                <a href="{{ route('admin.vendor-subscriptions.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
