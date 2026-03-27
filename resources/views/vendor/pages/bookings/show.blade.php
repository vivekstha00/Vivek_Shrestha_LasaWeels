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
@endphp

<div class="row g-4">
    <!-- Vehicle Image + Info -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body text-center">
                @if($booking->vehicle && $booking->vehicle->image)
                    <img src="{{ asset('storage/'.$booking->vehicle->image) }}"
                         alt="{{ $booking->vehicle->brand ?? '' }}"
                         class="img-fluid rounded mb-4"
                         style="max-height: 260px; object-fit: cover;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-4"
                         style="height: 260px;">
                        <i class="fas fa-car text-muted" style="font-size: 5rem;"></i>
                    </div>
                @endif

                <h5 class="fw-bold">{{ $booking->vehicle->brand ?? '' }} {{ $booking->vehicle->model ?? '' }}</h5>
                <p class="text-muted">{{ $booking->vehicle->registration_no ?? '' }}</p>
            </div>
        </div>
    </div>

    <!-- Booking Details -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Booking Information</h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="small text-muted">Customer</div>
                        <div class="fw-semibold">{{ $booking->user->name }}</div>
                        <small class="text-muted">{{ $booking->user->email }}</small>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Service</div>
                        <div class="fw-semibold">{{ ucfirst($booking->service ?? 'N/A') }}</div>
                    </div>

                    <div class="col-12">
                        <div class="small text-muted">Pickup Location</div>
                        <div class="fw-medium">{{ $booking->pickup_location }}</div>
                    </div>
                    <div class="col-12">
                        <div class="small text-muted">Drop Location</div>
                        <div class="fw-medium">{{ $booking->drop_location }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="small text-muted">Pickup Date</div>
                        <div class="fw-medium">{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Drop Date</div>
                        <div class="fw-medium">{{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y, h:i A') }}</div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-3">
                    <div>
                        <div class="small text-muted">Status</div>
                        <span class="badge bg-warning fs-6">{{ ucfirst($booking->status) }}</span>
                    </div>
                    <div>
                        <div class="small text-muted">Payment Status</div>
                        <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }} fs-6">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card bg-light mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Pricing Breakdown</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Original Amount</span>
                            <span>Rs. {{ number_format($originalAmount, 2) }}</span>
                        </div>
                        @if($loyaltyDiscount > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Loyalty Discount</span>
                            <span>- Rs. {{ number_format($loyaltyDiscount, 2) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between fw-bold border-top pt-2">
                            <span>Final Customer Payment</span>
                            <span>Rs. {{ number_format($booking->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
