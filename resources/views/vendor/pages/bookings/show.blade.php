@extends('vendor.layouts.master')

@section('title', 'Booking Details')
@section('page_title', 'Booking Details')
@section('page_subtitle', 'Booking #{{ $booking->id }}')

@section('vendor-content')
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Booking #{{ $booking->id }}</h2>
            <p class="text-muted">Detailed view of the booking</p>
        </div>
        <a href="{{ route('vendor.bookings.index') }}" class="btn btn-outline-secondary">Back to Bookings</a>
    </div>
</div>

@php
    $originalAmount = (float) $booking->total_price + (float) ($booking->loyalty_discount_amount ?? 0);
    $loyaltyDiscount = (float) ($booking->loyalty_discount_amount ?? 0);
    $vehicleImagePath = $booking->vehicle?->primaryImage?->path
        ?? $booking->vehicle?->images?->first()?->path
        ?? $booking->vehicle?->image_url;
@endphp

<div class="row g-4">
    <!-- Vehicle Image Row -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <!-- Image Container with Fixed Aspect Ratio -->
                <div class="position-relative mx-auto mb-3" style="aspect-ratio: 4/3; max-width: 350px; overflow: hidden; border-radius: 8px;">
                    @if($booking->vehicle && $vehicleImagePath)
                        <img src="{{ asset('storage/'.$vehicleImagePath) }}"
                             alt="{{ $booking->vehicle->brand ?? '' }} {{ $booking->vehicle->model ?? '' }}"
                             class="position-absolute top-0 start-0 w-100 h-100"
                             style="object-fit: cover;">
                    @else
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-car text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                </div>

                <!-- Vehicle Info -->
                <div>
                    <h5 class="fw-bold mb-1">{{ $booking->vehicle->brand ?? '' }} {{ $booking->vehicle->model ?? '' }}</h5>
                    <p class="text-muted small mb-0">{{ $booking->vehicle->registration_no ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details Row -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-4 text-primary">Booking Information</h5>

                <!-- Customer & Service Info -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user me-2"></i>Customer Details</h6>
                                <div class="mb-2">
                                    <span class="text-muted small">Name:</span>
                                    <span class="fw-semibold ms-2">{{ $booking->user->name }}</span>
                                </div>
                                <div>
                                    <span class="text-muted small">Email:</span>
                                    <span class="ms-2">{{ $booking->user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-cog me-2"></i>Service Details</h6>
                                <div class="mb-2">
                                    <span class="text-muted small">Service Type:</span>
                                    <span class="fw-semibold ms-2">{{ ucfirst($booking->service ?? 'N/A') }}</span>
                                </div>
                                <div>
                                    <span class="text-muted small">Booking Status:</span>
                                    <span class="badge bg-warning ms-2">{{ ucfirst($booking->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location & Date Info -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-map-marker-alt me-2"></i>Locations</h6>
                                <div class="mb-3">
                                    <span class="text-muted small d-block">Pickup Location:</span>
                                    <span class="fw-medium">{{ $booking->pickup_location }}</span>
                                </div>
                                <div>
                                    <span class="text-muted small d-block">Drop Location:</span>
                                    <span class="fw-medium">{{ $booking->drop_location }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-calendar me-2"></i>Schedule</h6>
                                <div class="mb-3">
                                    <span class="text-muted small d-block">Pickup Date & Time:</span>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y, h:i A') }}</span>
                                </div>
                                <div>
                                    <span class="text-muted small d-block">Drop Date & Time:</span>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-credit-card me-2"></i>Payment Status</h6>
                                <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }} fs-6 px-4 py-2">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm bg-gradient-light">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-rupee-sign me-2"></i>Pricing Breakdown</h6>
                                <div class="d-flex justify-content-between mb-2 py-1">
                                    <span class="fw-medium">Original Amount</span>
                                    <span class="fw-semibold">Rs. {{ number_format($originalAmount, 2) }}</span>
                                </div>
                                @if($loyaltyDiscount > 0)
                                <div class="d-flex justify-content-between mb-2 py-1 text-danger">
                                    <span class="fw-medium">Loyalty Discount</span>
                                    <span class="fw-semibold">- Rs. {{ number_format($loyaltyDiscount, 2) }}</span>
                                </div>
                                @endif
                                <hr class="my-2">
                                <div class="d-flex justify-content-between py-1">
                                    <span class="fw-bold">Final Payment</span>
                                    <span class="fw-bold fs-5 text-success">Rs. {{ number_format($booking->total_price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
