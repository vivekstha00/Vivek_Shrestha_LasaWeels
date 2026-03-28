@extends('admin.layouts.master')

@section('title', 'Review Details')

@section('admin-content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">Review Details</h3>
            <p class="text-muted mb-0">Booking #{{ $review->booking_id }}</p>
        </div>

        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    @php
        $vehicleName = $review->vehicle?->title
            ?? trim(($review->vehicle?->brand ?? '') . ' ' . ($review->vehicle?->model ?? ''));

        $booking = $review->booking;
        $bookingVehicle = $booking?->vehicle ?? $review->vehicle;

        $primaryImage = $bookingVehicle?->images?->firstWhere('is_primary', true)
            ?? $bookingVehicle?->images?->first();

        $vehicleImageSrc = null;
        if ($primaryImage?->path) {
            $vehicleImageSrc = str_starts_with($primaryImage->path, 'http://') || str_starts_with($primaryImage->path, 'https://')
                ? $primaryImage->path
                : (str_starts_with($primaryImage->path, 'storage/') ? asset($primaryImage->path) : asset('storage/' . ltrim($primaryImage->path, '/')));
        } elseif ($bookingVehicle?->image_url) {
            $vehicleImageSrc = str_starts_with($bookingVehicle->image_url, 'http://') || str_starts_with($bookingVehicle->image_url, 'https://')
                ? $bookingVehicle->image_url
                : (str_starts_with($bookingVehicle->image_url, 'storage/') ? asset($bookingVehicle->image_url) : asset('storage/' . ltrim($bookingVehicle->image_url, '/')));
        }

        $tripDays = null;
        if ($booking?->pickup_datetime && $booking?->drop_datetime) {
            $tripDays = $booking->pickup_datetime->startOfDay()->diffInDays($booking->drop_datetime->startOfDay()) + 1;
        }

        $amountPaid = $booking?->payment?->amount ?? $booking?->total_price;
    @endphp

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-suitcase-rolling me-2 text-primary"></i>Booking Snapshot
                </h5>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    @if($vehicleImageSrc)
                        <img src="{{ $vehicleImageSrc }}"
                             alt="{{ $vehicleName ?: 'Vehicle' }}"
                             class="w-100 rounded-3"
                             style="height: 210px; object-fit: cover;">
                    @else
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-muted bg-light"
                             style="height: 210px;">
                            <div class="text-center">
                                <i class="fa-solid fa-image fa-2x d-block mb-2 opacity-50"></i>
                                No vehicle image
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Vehicle Taken</div>
                            <div class="fw-semibold">{{ $vehicleName ?: 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Amount Paid</div>
                            <div class="fw-semibold text-success">{{ $amountPaid ? 'NPR ' . number_format((float) $amountPaid, 2) : 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Trip Duration</div>
                            <div class="fw-semibold">{{ $tripDays ? $tripDays . ' day' . ($tripDays > 1 ? 's' : '') : 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Payment Status</div>
                            <div class="fw-semibold text-capitalize">{{ $booking?->payment_status ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Pickup</div>
                            <div class="fw-semibold">{{ $booking?->pickup_location ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $booking?->pickup_datetime ? $booking->pickup_datetime->format('d M Y, h:i A') : '' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Drop</div>
                            <div class="fw-semibold">{{ $booking?->drop_location ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $booking?->drop_datetime ? $booking->drop_datetime->format('d M Y, h:i A') : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="small text-muted">User</div>
                    <div class="fw-semibold">{{ $review->user?->name ?? 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="small text-muted">Vehicle</div>
                    <div class="fw-semibold">{{ $vehicleName ?: 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="small text-muted">Vendor</div>
                    <div class="fw-semibold">{{ $review->vehicle?->vendor?->name ?? 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="small text-muted">Driver</div>
                    <div class="fw-semibold">{{ $review->driver?->name ?? 'Self Drive' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Overall Rating</div>
                    <div class="fw-semibold">{{ $review->overall_rating }}/5</div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Vehicle Rating</div>
                    <div class="fw-semibold">{{ $review->vehicle_rating }}/5</div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Driver Rating</div>
                    <div class="fw-semibold">{{ $review->driver_rating ? $review->driver_rating . '/5' : 'N/A' }}</div>
                </div>

                <div class="col-md-12">
                    <div class="small text-muted">Overall Review</div>
                    <div class="border rounded-3 p-3 bg-light">{{ $review->overall_review ?: 'No overall review written.' }}</div>
                </div>

                <div class="col-md-12">
                    <div class="small text-muted">Vehicle Review</div>
                    <div class="border rounded-3 p-3 bg-light">{{ $review->vehicle_review ?: 'No vehicle review written.' }}</div>
                </div>

                @if($review->driver_id)
                    <div class="col-md-12">
                        <div class="small text-muted">Driver Review</div>
                        <div class="border rounded-3 p-3 bg-light">{{ $review->driver_review ?: 'No driver review written.' }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
