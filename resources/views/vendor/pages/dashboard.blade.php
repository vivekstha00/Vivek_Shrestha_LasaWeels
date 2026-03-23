@extends('vendor.layouts.master')

@section('vendor-content')
<div class="mb-4">
    <h2 class="fw-bold mb-1">Vendor Dashboard</h2>
    <p class="text-muted mb-0">Monitor vehicles, drivers, bookings, and subscription usage.</p>
</div>

@if(isset($subscriptionSummary))
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Current Plan: {{ $subscriptionSummary['plan_name'] ?? 'Free Plan' }}</h5>
                    <p class="text-muted mb-0">
                        Status: <strong class="text-capitalize">{{ $subscriptionSummary['status'] ?? 'free' }}</strong>
                    </p>

                    @if(!empty($subscriptionSummary['ends_at']))
                        <p class="text-muted mb-0">
                            Expires On: {{ \Carbon\Carbon::parse($subscriptionSummary['ends_at'])->format('Y-m-d h:i A') }}
                        </p>
                    @endif
                </div>

                <div class="text-md-end">
                    <div><strong>Vehicles:</strong> {{ $subscriptionSummary['vehicle_count'] ?? 0 }} / {{ $subscriptionSummary['vehicle_limit'] ?? 2 }}</div>
                    <div><strong>Drivers:</strong> {{ $subscriptionSummary['driver_count'] ?? 0 }} / {{ $subscriptionSummary['driver_limit'] ?? 2 }}</div>
                </div>
            </div>

            @if(($subscriptionSummary['vehicle_count'] ?? 0) >= ($subscriptionSummary['vehicle_limit'] ?? 2)
                || ($subscriptionSummary['driver_count'] ?? 0) >= ($subscriptionSummary['driver_limit'] ?? 2))
                <div class="alert alert-warning rounded-3 mt-3 mb-0">
                    You have reached your current free-plan limit. Upgrade subscription to add more vehicles or drivers.
                </div>
            @endif
        </div>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-1">Total Vehicles</div>
                <h3 class="fw-bold mb-0">{{ $statistics['totalVehicles'] ?? 0 }}</h3>
                <small class="text-muted">Active: {{ $statistics['activeVehicles'] ?? 0 }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-1">Total Drivers</div>
                <h3 class="fw-bold mb-0">{{ $statistics['totalDrivers'] ?? 0 }}</h3>
                <small class="text-muted">Approved: {{ $statistics['approvedDrivers'] ?? 0 }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-1">Total Bookings</div>
                <h3 class="fw-bold mb-0">{{ $statistics['totalBookings'] ?? 0 }}</h3>
                <small class="text-muted">Pending: {{ $statistics['pendingBookings'] ?? 0 }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-1">Total Revenue</div>
                <h3 class="fw-bold mb-0">NPR {{ number_format($statistics['totalRevenue'] ?? 0, 2) }}</h3>
                <small class="text-muted">Completed payouts only</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-1">Pending Bookings</div>
                <h3 class="fw-bold mb-0">{{ $statistics['pendingBookings'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-1">Confirmed Bookings</div>
                <h3 class="fw-bold mb-0">{{ $statistics['confirmedBookings'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-1">Completed Bookings</div>
                <h3 class="fw-bold mb-0">{{ $statistics['completedBookings'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Recent Bookings</h5>
            <a href="{{ route('vendor.bookings.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                View All
            </a>
        </div>

        @if($recentBookings->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Pickup</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                <td>{{ $booking->vehicle->brand ?? '' }} {{ $booking->vehicle->model ?? '' }}</td>
                                <td>{{ optional($booking->pickup_datetime)->format('Y-m-d h:i A') }}</td>
                                <td>
                                    <span class="badge bg-secondary text-capitalize">{{ $booking->status }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('vendor.bookings.show', $booking) }}"
                                       class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No recent bookings found.</p>
        @endif
    </div>
</div>
@endsection
