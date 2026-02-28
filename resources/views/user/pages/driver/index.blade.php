@extends('user.layouts.master')

@section('user-content')
<div class="container mt-5 pt-5">

    @if(!empty($bookingData['vehicle_id']))
        <a href="{{ route('user.booking.create', $bookingData['vehicle_id']) }}?service={{ $bookingData['service'] ?? 'driver' }}&pickup_location={{ urlencode($bookingData['pickup_location'] ?? '') }}&drop_location={{ urlencode($bookingData['drop_location'] ?? '') }}&pickup_datetime={{ urlencode($bookingData['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($bookingData['drop_datetime'] ?? '') }}"
           class="btn btn-outline-secondary btn-sm mb-3">
            ← Back to Booking
        </a>
    @endif

    <h3 class="fw-bold mb-4">Choose a Driver</h3>

    @if(count($availableDrivers) > 0)
        <div class="row g-3">
            @foreach($availableDrivers as $driver)
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            @if($driver->image)
                                <img src="{{ asset('storage/'.$driver->image) }}"
                                     alt="{{ $driver->name }}"
                                     class="rounded-circle mb-3"
                                     style="width:80px;height:80px;object-fit:cover;">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                                     style="width:80px;height:80px;">
                                    <span class="text-muted fs-3 fw-bold">{{ strtoupper(substr($driver->name, 0, 1)) }}</span>
                                </div>
                            @endif

                            <h5 class="fw-bold mb-1">{{ $driver->name }}</h5>
                            <p class="text-muted small mb-1">Rating: {{ $driver->rating ?? 'N/A' }} / 5</p>
                            <p class="text-muted small mb-3">License: {{ $driver->license_number }}</p>

                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('user.driver.show', $driver->id) }}?vehicle_id={{ $bookingData['vehicle_id'] ?? '' }}&pickup_datetime={{ urlencode($bookingData['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($bookingData['drop_datetime'] ?? '') }}&pickup_location={{ urlencode($bookingData['pickup_location'] ?? '') }}&drop_location={{ urlencode($bookingData['drop_location'] ?? '') }}&service={{ $bookingData['service'] ?? 'driver' }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    View Details
                                </a>

                                @if(!empty($bookingData['vehicle_id']))
                                    <a href="{{ route('user.booking.create', $bookingData['vehicle_id']) }}?service={{ $bookingData['service'] ?? 'driver' }}&pickup_location={{ urlencode($bookingData['pickup_location'] ?? '') }}&drop_location={{ urlencode($bookingData['drop_location'] ?? '') }}&pickup_datetime={{ urlencode($bookingData['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($bookingData['drop_datetime'] ?? '') }}&driver_id={{ $driver->id }}"
                                       class="btn btn-success btn-sm">
                                        Select
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">
            No drivers available for the selected time. Please try different dates.
        </div>
    @endif
</div>
@endsection
