@extends('user.layouts.master')

@section('title', ($vehicle->brand . ' ' . $vehicle->model) . ' - Details')

@section('user-content')
@php
    $imgUrls = [];

    if ($vehicle->relationLoaded('images') && $vehicle->images->count()) {
        foreach ($vehicle->images as $img) {
            if (!empty($img->path)) {
                $imgUrls[] = asset('storage/' . ltrim($img->path, '/'));
            }
        }
    }

    if (empty($imgUrls) && $vehicle->primaryImage && !empty($vehicle->primaryImage->path)) {
        $imgUrls[] = asset('storage/' . ltrim($vehicle->primaryImage->path, '/'));
    }

    if (empty($imgUrls) && !empty($vehicle->image_url)) {
        $imgUrls[] = asset('storage/' . ltrim($vehicle->image_url, '/'));
    }

    if (empty($imgUrls)) {
        $imgUrls[] = 'https://via.placeholder.com/1200x700?text=No+Image';
    }
@endphp

<div class="container py-4">
    <div class="row g-4">

        {{-- LEFT: Images + Details --}}
        <div class="col-lg-8">

            {{-- Images Carousel --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div id="vehicleCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($imgUrls as $i => $url)
                            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                <img src="{{ $url }}"
                                     class="d-block w-100"
                                     style="height: 420px; object-fit: cover;"
                                     alt="Vehicle image {{ $i + 1 }}">
                            </div>
                        @endforeach
                    </div>

                    @if(count($imgUrls) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>

                @if(count($imgUrls) > 1)
                    <div class="p-3 bg-white border-top">
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($imgUrls as $i => $url)
                                <button type="button"
                                        class="border rounded-3 p-0 overflow-hidden"
                                        style="width: 72px; height: 52px;"
                                        data-bs-target="#vehicleCarousel"
                                        data-bs-slide-to="{{ $i }}"
                                        aria-label="Slide {{ $i + 1 }}">
                                    <img src="{{ $url }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Vehicle Information --}}
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-2">{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
                    <div class="text-muted mb-3">
                        {{ $vehicle->title ?? '' }}
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                            {{ ucfirst($vehicle->vehicle_type) }}
                        </span>
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                            {{ ucfirst(str_replace('_', ' ', $vehicle->wheel_type ?? '')) }}
                        </span>
                        <span class="badge px-3 py-2 rounded-pill
                            {{ $vehicle->fuel_type === 'electric'
                                ? 'bg-success-subtle text-success'
                                : 'bg-secondary-subtle text-secondary' }}">
                            {{ ucfirst($vehicle->fuel_type) }}
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4"><strong>Seats:</strong> {{ $vehicle->seating_capacity }}</div>
                        <div class="col-md-4"><strong>Transmission:</strong> {{ ucfirst($vehicle->transmission) }}</div>
                        <div class="col-md-4"><strong>Year:</strong> {{ $vehicle->manufacture_year }}</div>
                        <div class="col-md-6"><strong>Registration:</strong> {{ $vehicle->registration_no }}</div>
                        <div class="col-md-6"><strong>City:</strong> {{ $vehicle->location_city }}</div>

                        @if($vehicle->fuel_type === 'electric')
                            @if($vehicle->battery_capacity)
                                <div class="col-md-6">
                                    <strong>Battery Capacity:</strong> {{ $vehicle->battery_capacity }} kWh
                                </div>
                            @endif

                            @if($vehicle->range_per_charge)
                                <div class="col-md-6">
                                    <strong>Range per Charge:</strong> {{ $vehicle->range_per_charge }} km
                                </div>
                            @endif

                            @if($vehicle->charging_time)
                                <div class="col-md-6">
                                    <strong>Charging Time:</strong> {{ $vehicle->charging_time }} hrs
                                </div>
                            @endif

                            @if($vehicle->charger_type)
                                <div class="col-md-6">
                                    <strong>Charger Type:</strong> {{ $vehicle->charger_type }}
                                </div>
                            @endif
                        @else
                            @if($vehicle->mileage_per_litre)
                                <div class="col-md-6">
                                    <strong>Mileage:</strong> {{ $vehicle->mileage_per_litre }} km/l
                                </div>
                            @endif

                            @if($vehicle->fuel_tank_capacity)
                                <div class="col-md-6">
                                    <strong>Fuel Tank Capacity:</strong> {{ $vehicle->fuel_tank_capacity }} L
                                </div>
                            @endif
                        @endif

                        @if($vehicle->security_deposit)
                            <div class="col-md-6">
                                <strong>Security Deposit:</strong> Rs. {{ number_format($vehicle->security_deposit, 2) }}
                            </div>
                        @endif
                    </div>

                    @if($vehicle->description)
                        <hr class="my-4">
                        <h5 class="fw-bold">Description</h5>
                        <p class="mb-0 text-muted">{{ $vehicle->description }}</p>
                    @endif
                </div>
            </div>

            {{-- Maintenance & Reliability --}}
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Maintenance & Reliability</h5>

                    @if($latestService)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div><strong>Last Serviced:</strong> {{ $latestService->service_date?->format('d M Y') ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <strong>Status:</strong>
                                    @if($maintenanceStatus === 'Well maintained')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                            {{ $maintenanceStatus }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">
                                            {{ $maintenanceStatus }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                                <strong>Recent Maintenance:</strong>
                                <div class="text-muted mt-1">
                                    {{ $recentServiceItems->isNotEmpty() ? $recentServiceItems->join(', ') : 'Regular maintenance recorded' }}
                                </div>
                            </div>

                            <div class="col-md-12">
                                <strong>Service History:</strong>
                                <div class="text-muted mt-1">
                                    {{ $serviceCount }} maintenance visit{{ $serviceCount > 1 ? 's' : '' }} recorded
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-muted">
                            No maintenance history available for this vehicle yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: Simple booking card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 110px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Booking</h5>

                    @php
                        $selfPerDay   = (float) ($vehicle->price_per_day ?? 0);
                        $driverPerDay = (float) ($vehicle->with_driver_price_per_day ?? 0);
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted">Self Drive</div>
                        <div class="fw-bold">Rs. {{ number_format($selfPerDay, 2) }}/day</div>
                    </div>

                    @if($driverPerDay > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted">With Driver</div>
                            <div class="fw-bold">Rs. {{ number_format($driverPerDay, 2) }}/day</div>
                        </div>
                    @endif

                    <a href="{{ route('home', ['vehicle_id' => $vehicle->id]) }}#booking-form"
                       class="btn btn-success w-100 py-2">
                        Book This Vehicle
                    </a>

                    <a href="{{ route('vehicles.index') }}"
                       class="btn btn-outline-secondary w-100 py-2 mt-2">
                        Back to Vehicles
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
