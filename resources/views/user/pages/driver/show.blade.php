@extends('user.layouts.master')

@section('user-content')
<div class="container mt-5 pt-5">

    <a href="{{ route('user.driver.index') }}?vehicle_id={{ $bookingData['vehicle_id'] ?? '' }}&pickup_datetime={{ urlencode($bookingData['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($bookingData['drop_datetime'] ?? '') }}&pickup_location={{ urlencode($bookingData['pickup_location'] ?? '') }}&drop_location={{ urlencode($bookingData['drop_location'] ?? '') }}&service={{ $bookingData['service'] ?? 'driver' }}"
       class="btn btn-outline-secondary btn-sm mb-3">
        ← Back to Driver List
    </a>

    <h3 class="fw-bold mb-4">Driver Details</h3>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="row">
                {{-- Image --}}
                <div class="col-md-3 text-center border-end">
                    @if($driver->image)
                        <img src="{{ asset('storage/'.$driver->image) }}"
                             alt="{{ $driver->name }}"
                             class="rounded-circle mb-3"
                             style="width:120px;height:120px;object-fit:cover;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width:120px;height:120px;">
                            <span class="text-muted fs-2 fw-bold">{{ strtoupper(substr($driver->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1">{{ $driver->name }}</h5>
                    <p class="text-muted small mb-2">{{ $driver->phone ?? 'No phone' }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        @if($driver->availability_status === 'available')
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-secondary">Unavailable</span>
                        @endif
                    </div>
                </div>

                {{-- Details --}}
                <div class="col-md-9 ps-md-4 mt-4 mt-md-0">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small">Full Name</div>
                            <div class="fw-semibold">{{ $driver->name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Phone</div>
                            <div class="fw-semibold">{{ $driver->phone ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">License Number</div>
                            <div class="fw-semibold">{{ $driver->license_number }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Rating</div>
                            <div class="fw-semibold">{{ $driver->rating ? $driver->rating . ' / 5' : '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Availability</div>
                            <div class="fw-semibold">{{ ucfirst($driver->availability_status) }}</div>
                        </div>
                    </div>

                    {{-- Select this driver button --}}
                    @if(!empty($bookingData['vehicle_id']))
                        <div class="mt-4">
                            <a href="{{ route('user.booking.create', $bookingData['vehicle_id']) }}?service={{ $bookingData['service'] ?? 'driver' }}&pickup_location={{ urlencode($bookingData['pickup_location'] ?? '') }}&drop_location={{ urlencode($bookingData['drop_location'] ?? '') }}&pickup_datetime={{ urlencode($bookingData['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($bookingData['drop_datetime'] ?? '') }}&driver_id={{ $driver->id }}"
                               class="btn btn-success">
                                Select this Driver
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
