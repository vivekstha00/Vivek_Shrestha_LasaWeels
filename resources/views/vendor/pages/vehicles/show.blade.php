@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">{{ $vehicle->title }}</h3>
            <div class="text-muted small">
                {{ $vehicle->brand }} • {{ $vehicle->model }} • Reg: {{ $vehicle->registration_no }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('vendor.vehicles.edit', $vehicle->id) }}" class="btn btn-outline-dark">
                Edit
            </a>

            <form method="POST" action="{{ route('vendor.vehicles.destroy', $vehicle->id) }}"
                  onsubmit="return confirm('Delete this vehicle?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Delete</button>
            </form>

            <a href="{{ route('vendor.vehicles.index') }}" class="btn btn-outline-secondary">
                Back
            </a>
        </div>
    </div>

    <div class="row g-3">
        {{-- Images --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">

                    @php
                        // choose primary image first, else first image, else old image_url, else null
                        $primary = $vehicle->images->firstWhere('is_primary', true)
                                    ?? $vehicle->images->first();
                        $mainSrc = $primary ? asset('storage/'.$primary->path)
                                 : ($vehicle->image_url ? asset('storage/'.$vehicle->image_url) : null);
                    @endphp

                    <div class="mb-3">
                        @if($mainSrc)
                            <img id="mainVehicleImage"
                                 src="{{ $mainSrc }}"
                                 alt="Vehicle image"
                                 style="width:100%;height:340px;object-fit:cover;border-radius:10px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                                 style="width:100%;height:340px;border-radius:10px;">
                                No images uploaded
                            </div>
                        @endif
                    </div>

                    {{-- Thumbnails --}}
                    @if($vehicle->images && $vehicle->images->count() > 0)
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($vehicle->images as $img)
                                <img
                                    src="{{ asset('storage/'.$img->path) }}"
                                    alt="thumb"
                                    onclick="setMainImage('{{ asset('storage/'.$img->path) }}')"
                                    style="width:72px;height:52px;object-fit:cover;border-radius:8px;cursor:pointer;border:1px solid rgba(0,0,0,.1);">
                            @endforeach
                        </div>
                    @endif

                    <script>
                        function setMainImage(src) {
                            const main = document.getElementById('mainVehicleImage');
                            if (main) main.src = src;
                        }
                    </script>

                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="d-flex align-items-center gap-2 mb-3">
                        @if($vehicle->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($vehicle->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif

                        <span class="badge bg-secondary">
                            Active: {{ $vehicle->is_active ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    @if($vehicle->status === 'rejected' && $vehicle->reject_reason)
                        <div class="alert alert-danger">
                            <strong>Reject Reason:</strong> {{ $vehicle->reject_reason }}
                        </div>
                    @endif

                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="small text-muted">Vehicle Type</div>
                            <div class="fw-semibold">{{ $vehicle->vehicle_type }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Wheel Type</div>
                            <div class="fw-semibold">{{ $vehicle->wheel_type ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Brand</div>
                            <div class="fw-semibold">{{ $vehicle->brand }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Model</div>
                            <div class="fw-semibold">{{ $vehicle->model }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Fuel Type</div>
                            <div class="fw-semibold">{{ $vehicle->fuel_type }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Transmission</div>
                            <div class="fw-semibold">{{ $vehicle->transmission }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Seats</div>
                            <div class="fw-semibold">{{ $vehicle->seating_capacity }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Mileage / Litre</div>
                            <div class="fw-semibold">{{ $vehicle->mileage_per_litre ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Manufacture Year</div>
                            <div class="fw-semibold">{{ $vehicle->manufacture_year ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Price / Day</div>
                            <div class="fw-semibold">
                                {{ number_format($vehicle->price_per_day, 2) }} {{ $vehicle->currency }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">With Driver / Day</div>
                            <div class="fw-semibold">
                                {{ $vehicle->with_driver_price_per_day ? number_format($vehicle->with_driver_price_per_day, 2).' '.$vehicle->currency : '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Security Deposit</div>
                            <div class="fw-semibold">
                                {{ number_format($vehicle->security_deposit ?? 0, 2) }} {{ $vehicle->currency }}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="small text-muted">Location</div>
                            <div class="fw-semibold">
                                {{ $vehicle->location_city }}
                                @if($vehicle->location_area) • {{ $vehicle->location_area }} @endif
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="small text-muted">Pickup Address</div>
                            <div class="fw-semibold">{{ $vehicle->pickup_address ?? '—' }}</div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="small text-muted">Description</div>
                            <div class="fw-semibold">{{ $vehicle->description ?? '—' }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection
