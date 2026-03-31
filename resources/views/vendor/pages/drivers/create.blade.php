@extends('vendor.layouts.master')

@section('title', 'Add New Driver')
@section('page_title', 'Add New Driver')
@section('page_subtitle', 'Add a new driver to your team')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Add New Driver</h2>
    <p class="text-muted">Fill in the driver information below</p>
</div>

@if(session('error'))
    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">License Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="license_number" value="{{ old('license_number') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Availability Status</label>
                    <select class="form-select" name="availability_status" required>
                        <option value="available" {{ old('availability_status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ old('availability_status') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Rating (Optional)</label>
                    <input type="number" class="form-control" name="rating" min="0" max="5" step="0.1" value="{{ old('rating') }}">
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
