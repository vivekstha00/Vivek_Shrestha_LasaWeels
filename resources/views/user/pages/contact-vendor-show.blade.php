@extends('user.layouts.master')

@section('title', 'Vendor Details')

@section('user-content')
<div class="container py-5 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Vendor Details</h3>
            <p class="text-muted mb-0">Verified vendor information and location</p>
        </div>
        <a href="{{ route('contact.create') }}" class="btn btn-outline-secondary">Back to Contact</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Vendor Details</h5>
                    <p class="mb-2"><strong>Name:</strong> {{ $vendor->vendorProfile->business_name ?? $vendor->name }}</p>
                    <p class="mb-2"><strong>Business Type:</strong>
                        {{ ucfirst($vendor->vendorProfile->business_type ?? 'vendor') }}
                    </p>
                    <p class="mb-2"><strong>Contact Number:</strong>
                        {{ $vendor->vendorProfile->phone ?? $vendor->phone ?? 'N/A' }}
                    </p>
                    <p class="mb-2"><strong>Email:</strong> {{ $vendor->email ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Address:</strong>
                        {{ $vendor->vendorProfile->business_address ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 h-100 bg-light">
                <div class="card-body p-4 d-flex flex-column justify-content-start">
                    <p class="text-muted small mb-1">Need directions?</p>
                    <h6 class="fw-semibold mb-3">Quick Actions</h6>
                    @if(!empty($vendor->vendorProfile->latitude) && !empty($vendor->vendorProfile->longitude))
                        <div class="d-grid gap-2">
                            <a href="https://www.google.com/maps?q={{ $vendor->vendorProfile->latitude }},{{ $vendor->vendorProfile->longitude }}"
                               target="_blank"
                               class="btn btn-outline-dark">
                                Open in Google Maps
                            </a>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $vendor->vendorProfile->latitude }},{{ $vendor->vendorProfile->longitude }}"
                               target="_blank"
                               class="btn btn-dark">
                                Get Directions
                            </a>
                        </div>
                    @else
                        <p class="text-muted mb-0">Map coordinates are not available for this vendor.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light border-0 rounded-top-4 py-3">
            <h6 class="mb-0 fw-semibold">Location Map</h6>
        </div>
        <div class="card-body p-0">
            @if(!empty($vendor->vendorProfile->latitude) && !empty($vendor->vendorProfile->longitude))
                <iframe
                    src="https://maps.google.com/maps?q={{ $vendor->vendorProfile->latitude }},{{ $vendor->vendorProfile->longitude }}&z=15&output=embed"
                    width="100%"
                    height="420"
                    style="border:0; border-radius: 0 0 1rem 1rem;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            @else
                <div class="p-4 text-muted">Location map is unavailable because coordinates are missing.</div>
            @endif
        </div>
    </div>
</div>
@endsection
