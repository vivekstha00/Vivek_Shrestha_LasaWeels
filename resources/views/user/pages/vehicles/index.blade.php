@extends('user.layouts.master')

@section('title', 'All Vehicles')

@section('user-content')
<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">All Vehicles</h1>
        <p class="text-muted">Browse all available vehicles</p>
    </div>

    <div class="row">
        @forelse($vehicles as $vehicle)
            @php
                $img = $vehicle->primaryImage
                    ? asset('storage/' . ltrim($vehicle->primaryImage->path, '/'))
                    : ($vehicle->image_url
                        ? asset('storage/' . ltrim($vehicle->image_url, '/'))
                        : 'https://via.placeholder.com/600x400?text=No+Image');
            @endphp

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <img src="{{ $img }}" class="card-img-top" style="height: 220px; object-fit: cover;" alt="{{ $vehicle->brand }} {{ $vehicle->model }}">

                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold mb-1">{{ $vehicle->brand }} {{ $vehicle->model }}</h5>
                        <p class="text-muted small mb-2">
                            {{ ucfirst($vehicle->fuel_type) }} ·
                            {{ ucfirst($vehicle->transmission) }} ·
                            Seats {{ $vehicle->seats }}
                        </p>

                        <div class="fw-bold text-success fs-5 mb-3">
                            Rs. {{ number_format($vehicle->price_per_day, 2) }}/day
                        </div>

                        <a href="{{ route('vehicles.browse.show', $vehicle->id) }}" class="btn btn-success mt-auto rounded-3">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4">
                    No vehicles available right now.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $vehicles->links() }}
    </div>
</div>
@endsection
