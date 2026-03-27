@extends('vendor.layouts.master')

@section('vendor-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">My Vehicles</h2>
        <p class="text-muted mb-0">Manage your vehicle listings and subscription usage.</p>
    </div>
    <a href="{{ route('vendor.vehicles.create') }}" class="btn btn-primary rounded-pill px-4">
        + Add Vehicle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
@endif

@if(isset($subscriptionSummary))
    <div class="alert alert-info rounded-3 mb-4">
        <strong>Current Plan:</strong> {{ $subscriptionSummary['plan_name'] ?? 'Free Plan' }}<br>
        <strong>Vehicle Usage:</strong> {{ $subscriptionSummary['vehicle_count'] ?? 0 }} / {{ $subscriptionSummary['vehicle_limit'] ?? 2 }}

        @if(!empty($subscriptionSummary['ends_at']))
            <br><strong>Expires On:</strong> {{ \Carbon\Carbon::parse($subscriptionSummary['ends_at'])->format('Y-m-d h:i A') }}
        @endif
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if($vehicles->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Registration</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicle)
                            <tr
                                style="cursor: pointer;"
                                onclick="window.location='{{ route('vendor.vehicles.show', $vehicle) }}'"
                                onmouseover="this.style.backgroundColor='#f8f9fa'"
                                onmouseout="this.style.backgroundColor=''"
                            >
                                <td>
                                    <div class="fw-semibold">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                    <small class="text-muted text-capitalize">
                                        {{ str_replace('_', ' ', $vehicle->wheel_type) }}
                                        | {{ $vehicle->vehicle_type }}
                                        | {{ $vehicle->fuel_type }}
                                    </small>
                                </td>
                                <td>{{ $vehicle->registration_no }}</td>
                                <td>NPR {{ number_format($vehicle->price_per_day, 2) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($vehicle->status) {
                                            'available' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            'maintenance' => 'bg-secondary',
                                            'inactive' => 'bg-dark',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} text-capitalize">
                                        {{ str_replace('_', ' ', $vehicle->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($vehicle->is_active)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $vehicles->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No vehicles found</h5>
                <p class="text-muted mb-3">Start by adding your first vehicle listing.</p>
                <a href="{{ route('vendor.vehicles.create') }}" class="btn btn-primary rounded-pill px-4">
                    Add Vehicle
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
