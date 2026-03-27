@extends('vendor.layouts.master')

@section('title', 'My Vehicles')
@section('page_title', 'My Vehicles')
@section('page_subtitle', 'Manage your vehicle listings and subscription usage.')

@section('vendor-content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1">My Vehicles</h2>
        <p class="text-muted mb-0">Manage your vehicle listings and subscription usage.</p>
    </div>
    <a href="{{ route('vendor.vehicles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Vehicle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
@endif

@if(isset($subscriptionSummary))
    <div class="card subscription-box mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Current Plan:</strong> {{ $subscriptionSummary['plan_name'] ?? 'Basic Monthly' }}
                    <br>
                    <strong>Vehicle Usage:</strong> {{ $subscriptionSummary['vehicle_count'] ?? 0 }} / {{ $subscriptionSummary['vehicle_limit'] ?? 10 }}
                </div>
                @if(!empty($subscriptionSummary['ends_at']))
                    <div class="text-muted small">
                        Expires: {{ \Carbon\Carbon::parse($subscriptionSummary['ends_at'])->format('d M Y, h:i A') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        @if($vehicles->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
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
                            <tr style="cursor: pointer;"
                                onclick="window.location='{{ route('vendor.vehicles.show', $vehicle) }}'">
                                <td>
                                    <div class="fw-semibold">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                    <small class="text-muted">
                                        {{ str_replace('_', ' ', $vehicle->wheel_type) }} |
                                        {{ $vehicle->vehicle_type }} |
                                        {{ $vehicle->fuel_type }}
                                    </small>
                                </td>
                                <td>{{ $vehicle->registration_no }}</td>
                                <td class="fw-medium">NPR {{ number_format($vehicle->price_per_day, 2) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($vehicle->status) {
                                            'available' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'maintenance' => 'bg-secondary',
                                            default => 'bg-secondary'
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
            <div class="p-4 border-top">
                {{ $vehicles->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold text-muted">No vehicles found</h5>
                <p class="text-muted mb-3">Start by adding your first vehicle.</p>
                <a href="{{ route('vendor.vehicles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Add Vehicle
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
