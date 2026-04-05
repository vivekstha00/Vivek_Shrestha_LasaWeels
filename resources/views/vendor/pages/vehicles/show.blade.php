@extends('vendor.layouts.master')

@section('title', 'Vehicles - Vendor')
@section('page_title', 'Vehicles - Details')
@section('page_subtitle', 'View details of your vehicle listing')

@section('vendor-content')

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
                    $primary = $vehicle->images->firstWhere('is_primary', true) ?? $vehicle->images->first();
                    $mainSrc = $primary
                        ? asset('storage/' . $primary->path)
                        : ($vehicle->image_url ? asset('storage/' . $vehicle->image_url) : null);
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

                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                    @switch($vehicle->status)
                        @case('available')
                            <span class="badge bg-primary">Available</span>
                            @break

                        @case('rented')
                            <span class="badge bg-info text-dark">Rented</span>
                            @break

                        @case('maintenance')
                            <span class="badge bg-secondary">Maintenance</span>
                            @break

                        @case('inactive')
                            <span class="badge bg-dark">Inactive</span>
                            @break

                        @case('pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                            @break

                        @case('rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @break

                        @default
                            <span class="badge bg-light text-dark">{{ ucfirst($vehicle->status) }}</span>
                    @endswitch

                    <span class="badge bg-secondary">
                        Active: {{ $vehicle->is_active ? 'Yes' : 'No' }}
                    </span>
                </div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="small text-muted">Vehicle Type</div>
                        <div class="fw-semibold text-capitalize">{{ $vehicle->vehicle_type }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="small text-muted">Wheel Type</div>
                        <div class="fw-semibold">{{ str_replace('_', ' ', $vehicle->wheel_type ?? '—') }}</div>
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
                        <div class="fw-semibold">{{ ucfirst($vehicle->fuel_type) }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="small text-muted">Transmission</div>
                        <div class="fw-semibold">{{ ucfirst($vehicle->transmission) }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="small text-muted">{{ $vehicle->wheel_type === '2_wheeler' ? 'Rider Capacity' : 'Seats' }}</div>
                        <div class="fw-semibold">{{ $vehicle->seating_capacity }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="small text-muted">Manufacture Year</div>
                        <div class="fw-semibold">{{ $vehicle->manufacture_year ?? '—' }}</div>
                    </div>

                    @if($vehicle->fuel_type === 'electric')
                        <div class="col-md-6">
                            <div class="small text-muted">Battery Capacity</div>
                            <div class="fw-semibold">
                                {{ $vehicle->battery_capacity ? $vehicle->battery_capacity . ' kWh' : '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Range per Charge</div>
                            <div class="fw-semibold">
                                {{ $vehicle->range_per_charge ? $vehicle->range_per_charge . ' km' : '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Charging Time</div>
                            <div class="fw-semibold">
                                {{ $vehicle->charging_time ? $vehicle->charging_time . ' hrs' : '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Charger Type</div>
                            <div class="fw-semibold">{{ $vehicle->charger_type ?? '—' }}</div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <div class="small text-muted">Mileage / Litre</div>
                            <div class="fw-semibold">{{ $vehicle->mileage_per_litre ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Fuel Tank Capacity</div>
                            <div class="fw-semibold">
                                {{ $vehicle->fuel_tank_capacity ? $vehicle->fuel_tank_capacity . ' L' : '—' }}
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="small text-muted">Price / Day</div>
                        <div class="fw-semibold">
                            {{ number_format($vehicle->price_per_day, 2) }} {{ $vehicle->currency ?? 'NPR' }}
                        </div>
                    </div>

                    @if($vehicle->wheel_type !== '2_wheeler')
                        <div class="col-md-6">
                            <div class="small text-muted">With Driver / Day</div>
                            <div class="fw-semibold">
                                {{ $vehicle->with_driver_price_per_day ? number_format($vehicle->with_driver_price_per_day, 2).' '.($vehicle->currency ?? 'NPR') : '—' }}
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="small text-muted">Security Deposit</div>
                        <div class="fw-semibold">
                            {{ number_format($vehicle->security_deposit ?? 0, 2) }} {{ $vehicle->currency ?? 'NPR' }}
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

                    @if(!empty($vehicle->reject_reason))
                        <div class="col-md-12 mt-2">
                            <div class="alert alert-danger mb-0">
                                <strong>Reject Reason:</strong> {{ $vehicle->reject_reason }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Service Summary --}}
<div class="row g-3 mt-1">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="small text-muted">Last Service</div>
                <div class="fw-semibold">
                    {{ $latestService?->service_date?->format('d M Y') ?? '—' }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="small text-muted">Next Due Date</div>
                <div class="fw-semibold">
                    {{ $latestService?->next_service_due_date?->format('d M Y') ?? '—' }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="small text-muted">Total Maintenance Cost</div>
                <div class="fw-semibold">
                    {{ number_format($totalServiceCost ?? 0, 2) }} {{ $vehicle->currency ?? 'NPR' }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="small text-muted">Service Alert</div>
                <div class="fw-semibold">
                    @if($serviceAlert === 'overdue')
                        <span class="badge bg-danger">Overdue</span>
                    @elseif($serviceAlert === 'due_soon')
                        <span class="badge bg-warning text-dark">Due Soon</span>
                    @else
                        <span class="badge bg-success">OK</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Verification Documents --}}
<div class="card shadow-sm mt-3">
    <div class="card-body">
        <h5 class="mb-3">Verification Documents</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="small text-muted">Registration Document</div>
                @if($vehicle->vehicle_registration_document_path)
                    <a href="{{ asset('storage/' . $vehicle->vehicle_registration_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                        View
                    </a>
                @else
                    <div class="fw-semibold">—</div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="small text-muted">Insurance Document</div>
                @if($vehicle->insurance_document_path)
                    <a href="{{ asset('storage/' . $vehicle->insurance_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                        View
                    </a>
                @else
                    <div class="fw-semibold">—</div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="small text-muted">Insurance Expiry Date</div>
                <div class="fw-semibold">{{ optional($vehicle->insurance_expiry_date)->format('d M Y') ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Service History --}}
<div class="card shadow-sm mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0">Service History</h5>
                <div class="text-muted small">Track maintenance records of this vehicle</div>
            </div>

            <a href="{{ route('vendor.vehicles.services.create', $vehicle->id) }}"
               class="btn btn-primary btn-sm">
                + Add Service Record
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Service Date</th>
                        <th>Type</th>
                        <th>Odometer</th>
                        <th>Cost</th>
                        <th>Next Due Date</th>
                        <th>Next Due KM</th>
                        <th style="width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicle->services as $service)
                        <tr>
                            <td>{{ $service->service_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $service->service_type)) }}</td>
                            <td>{{ $service->odometer_km ? number_format($service->odometer_km).' km' : '—' }}</td>
                            <td>
                                {{ $service->cost ? number_format($service->cost, 2).' '.($vehicle->currency ?? 'NPR') : '—' }}
                            </td>
                            <td>{{ $service->next_service_due_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $service->next_service_due_km ? number_format($service->next_service_due_km).' km' : '—' }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('vendor.vehicles.services.edit', [$vehicle->id, $service->id]) }}"
                                   class="btn btn-sm btn-outline-dark">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('vendor.vehicles.services.destroy', [$vehicle->id, $service->id]) }}"
                                      onsubmit="return confirm('Delete this service record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>

                        @if($service->notes)
                            <tr>
                                <td colspan="7" class="small text-muted">
                                    <strong>Notes:</strong> {{ $service->notes }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No service records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
