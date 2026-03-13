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
    @endphp

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
