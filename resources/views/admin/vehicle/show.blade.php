@extends('admin.layouts.master')

@section('title', 'Vehicle Details')

@section('admin-content')
<div class="mb-4">
    <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Vehicles
    </a>
    <h2 class="fw-bold mb-1">Vehicle Details</h2>
    <p class="text-muted mb-0">View vehicle information, images, and manage status</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">

        <!-- Image Gallery -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-images me-2 text-primary"></i> Vehicle Images
                </h5>

                @if($vehicle->images->count())
                    <div class="row g-3">
                        @foreach($vehicle->images as $image)
                            @php
                                $imgPath = $image->path;
                                $imgSrc = str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')
                                    ? $imgPath
                                    : (str_starts_with($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/')));
                            @endphp
                            <div class="col-md-4 col-6">
                                <div class="position-relative rounded-3 overflow-hidden" style="aspect-ratio: 4/3; background: #f3f0ec;">
                                    <img src="{{ $imgSrc }}"
                                         alt="Vehicle Image"
                                         class="w-100 h-100"
                                         style="object-fit: cover;">
                                    @if($image->is_primary)
                                        <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                            Primary
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($vehicle->image_url)
                    @php
                        $fallbackPath = $vehicle->image_url;
                        $fallbackSrc = str_starts_with($fallbackPath, 'http://') || str_starts_with($fallbackPath, 'https://')
                            ? $fallbackPath
                            : (str_starts_with($fallbackPath, 'storage/') ? asset($fallbackPath) : asset('storage/' . ltrim($fallbackPath, '/')));
                    @endphp
                    <div class="rounded-3 overflow-hidden" style="max-height: 350px; background: #f3f0ec;">
                        <img src="{{ $fallbackSrc }}"
                             alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                             class="w-100" style="object-fit: cover; max-height: 350px;">
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-image fa-3x mb-2 d-block opacity-25"></i>
                        No images uploaded.
                    </div>
                @endif
            </div>
        </div>

        <!-- Vehicle Details -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-car me-2 text-primary"></i> Vehicle Information
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Brand</div>
                        <div class="fw-semibold">{{ $vehicle->brand }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Model</div>
                        <div class="fw-semibold">{{ $vehicle->model }}</div>
                    </div>
                    @if($vehicle->variant)
                        <div class="col-md-6">
                            <div class="text-muted small">Variant</div>
                            <div class="fw-semibold">{{ $vehicle->variant }}</div>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <div class="text-muted small">Type</div>
                        <div class="fw-semibold text-capitalize">{{ $vehicle->vehicle_type }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Wheel Type</div>
                        <div class="fw-semibold text-capitalize">{{ $vehicle->wheel_type ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Registration No.</div>
                        <div class="fw-semibold">{{ $vehicle->registration_no ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Year</div>
                        <div class="fw-semibold">{{ $vehicle->manufacture_year ?? '—' }}</div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Fuel Type</div>
                        <div class="fw-semibold text-capitalize">{{ $vehicle->fuel_type ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Transmission</div>
                        <div class="fw-semibold text-capitalize">{{ $vehicle->transmission ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Seating</div>
                        <div class="fw-semibold">{{ $vehicle->seating_capacity ?? '—' }} seats</div>
                    </div>
                    @if($vehicle->mileage_per_litre)
                        <div class="col-md-4">
                            <div class="text-muted small">Mileage</div>
                            <div class="fw-semibold">{{ $vehicle->mileage_per_litre }} km/l</div>
                        </div>
                    @endif
                </div>

                @if($vehicle->description)
                    <hr class="my-3">
                    <div class="text-muted small">Description</div>
                    <div>{{ $vehicle->description }}</div>
                @endif

                @if($vehicle->location_city || $vehicle->location_area)
                    <hr class="my-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i> City</div>
                            <div class="fw-semibold">{{ $vehicle->location_city ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small"><i class="fa-solid fa-map-pin me-1"></i> Area</div>
                            <div class="fw-semibold">{{ $vehicle->location_area ?? '—' }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">

        <!-- Status -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 text-center">
                @php
                    $statusConfig = match($vehicle->status) {
                        'approved' => ['icon' => 'fa-circle-check', 'gradient' => 'linear-gradient(135deg, #22c55e, #16a34a)', 'label' => 'Approved', 'color' => 'text-success'],
                        'pending' => ['icon' => 'fa-clock', 'gradient' => 'linear-gradient(135deg, #f59e0b, #d97706)', 'label' => 'Pending', 'color' => 'text-warning'],
                        'rejected' => ['icon' => 'fa-circle-xmark', 'gradient' => 'linear-gradient(135deg, #ef4444, #dc2626)', 'label' => 'Rejected', 'color' => 'text-danger'],
                        default => ['icon' => 'fa-circle-question', 'gradient' => 'linear-gradient(135deg, #9ca3af, #6b7280)', 'label' => ucfirst($vehicle->status), 'color' => 'text-secondary'],
                    };
                @endphp
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 56px; height: 56px; background: {{ $statusConfig['gradient'] }}; color: #fff;">
                    <i class="fa-solid {{ $statusConfig['icon'] }} fa-lg"></i>
                </div>
                <h5 class="fw-bold {{ $statusConfig['color'] }} mb-0">{{ $statusConfig['label'] }}</h5>
                <div class="text-muted small">Active: {{ $vehicle->is_active ? 'Yes' : 'No' }}</div>

                @if($vehicle->reject_reason)
                    <div class="alert alert-danger mt-2 mb-0 small text-start">
                        <strong>Reason:</strong> {{ $vehicle->reject_reason }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Pricing -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-tag me-2 text-primary"></i> Pricing
                </h5>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted">Per Day</span>
                    <span class="fw-bold text-primary">NPR {{ number_format($vehicle->price_per_day, 2) }}</span>
                </div>
                @if($vehicle->with_driver_price_per_day)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">With Driver</span>
                        <span class="fw-bold">NPR {{ number_format($vehicle->with_driver_price_per_day, 2) }}</span>
                    </div>
                @endif
                @if($vehicle->security_deposit)
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted">Security Deposit</span>
                        <span class="fw-bold">NPR {{ number_format($vehicle->security_deposit, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Vendor Info -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-building me-2 text-primary"></i> Vendor
                </h5>
                <div class="fw-semibold">{{ $vehicle->vendor?->name ?? '—' }}</div>
                <div class="text-muted small">{{ $vehicle->vendor?->email ?? '' }}</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-gear me-2 text-primary"></i> Actions
                </h5>

                @if($vehicle->status === 'pending')
                    <form method="POST" action="{{ route('admin.vehicles.approve', $vehicle) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-check me-1"></i> Approve
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.vehicles.reject', $vehicle) }}">
                        @csrf
                        <input type="hidden" name="reject_reason" value="Rejected by admin">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fa-solid fa-xmark me-1"></i> Reject
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.vehicles.toggleActive', $vehicle) }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary w-100">
                        {{ $vehicle->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
