@extends('vendor.layouts.master')

@section('title', 'Add New Driver')
@section('page_title', 'Add New Driver')
@section('page_subtitle', 'Add a new driver to your team')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Add New Driver</h2>
    <p class="text-muted">Fill in the driver information below</p>
</div>

@if(isset($subscriptionSummary))
    <div class="alert {{ ($canAddDriver ?? false) ? 'alert-info' : 'alert-warning' }} mb-4">
        <strong>Current Plan:</strong> {{ $subscriptionSummary['plan_name'] ?? 'Free Plan' }}<br>
        <strong>Driver Usage:</strong> {{ $subscriptionSummary['driver_count'] ?? 0 }} / {{ $subscriptionSummary['driver_limit'] ?? 8 }}
        @if(!($canAddDriver ?? true))
            <hr class="my-2">
            Free plan limit reached. Upgrade your subscription to add more drivers.
        @endif
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('vendor.drivers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Driver Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="phone" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">License Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="license_number" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Availability Status</label>
                    <select class="form-select" name="availability_status" required>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Rating (Optional)</label>
                    <input type="number" class="form-control" name="rating" min="0" max="5" step="0.1">
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Profile Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <small class="text-muted">Recommended size: 300x300 px</small>
                </div>
            </div>

            <div class="mt-5">
                <button type="submit" class="btn btn-primary px-5" {{ !($canAddDriver ?? true) ? 'disabled' : '' }}>
                    Save Driver
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
