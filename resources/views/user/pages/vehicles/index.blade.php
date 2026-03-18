@extends('user.layouts.master')

@section('title', 'All Vehicles')

@section('user-content')
<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">All Vehicles</h1>
        <p class="text-muted">Browse all available vehicles</p>
    </div>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('vehicles.index') }}">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fuel Type</label>
                        <select name="fuel_type" class="form-select rounded-3">
                            <option value="">All</option>
                            <option value="petrol" {{ request('fuel_type') == 'petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="diesel" {{ request('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="electric" {{ request('fuel_type') == 'electric' ? 'selected' : '' }}>Electric</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Transmission</label>
                        <select name="transmission" class="form-select rounded-3">
                            <option value="">All</option>
                            <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }}>Automatic</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Min Price</label>
                        <input
                            type="number"
                            name="min_price"
                            class="form-control rounded-3"
                            placeholder="5000"
                            value="{{ request('min_price') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Max Price</label>
                        <input
                            type="number"
                            name="max_price"
                            class="form-control rounded-3"
                            placeholder="15000"
                            value="{{ request('max_price') }}"
                        >
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-success w-100 rounded-3">
                            Apply Filters
                        </button>

                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary w-100 rounded-3">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
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
                <a href="{{ route('vehicles.browse.show', $vehicle->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 0.5rem 1rem rgba(0, 0, 0, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';">
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

                            <button class="btn btn-success mt-auto rounded-3" onclick="event.preventDefault(); location.href='{{ route('vehicles.browse.show', $vehicle->id) }}';">
                                View Details
                            </button>
                        </div>
                    </div>
                </a>
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
