@extends('vendor.layouts.master')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Vendor Dashboard</h2>
    <p class="text-muted">Monitor vehicles, drivers, bookings, and subscription usage.</p>
</div>

<!-- Current Plan -->
@if(isset($subscriptionSummary))
<div class="card subscription-box mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="fw-bold">Current Plan: {{ $subscriptionSummary['plan_name'] ?? 'Basic' }}</h5>
                <p class="mb-1">Status: <strong class="text-success">Active</strong></p>
                @if(!empty($subscriptionSummary['ends_at']))
                <p class="mb-0 text-muted">
                    Expires: {{ \Carbon\Carbon::parse($subscriptionSummary['ends_at'])->format('d M Y, h:i A') }}
                </p>
                @endif
            </div>
            <div class="text-end">
                <div><strong>Vehicles:</strong> {{ $subscriptionSummary['vehicle_count'] ?? 0 }} / 10</div>
                <div><strong>Drivers:</strong> {{ $subscriptionSummary['driver_count'] ?? 0 }} / 8</div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Total Vehicles</div>
                <h3 class="fw-bold mb-1">{{ $statistics['totalVehicles'] ?? 0 }}</h3>
                <small class="text-success">Active: {{ $statistics['activeVehicles'] ?? 0 }}</small>
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

<!-- Recent Bookings -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Recent Bookings</h5>
            <a href="{{ route('vendor.bookings.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
        </div>
        <!-- Table remains similar but will look much better with new CSS -->
        @if($recentBookings->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Booking</th>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Pickup</th>
                            <th>Drop</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                            @php
                                $statusClass = match($booking->status) {
                                    'completed' => 'bg-success',
                                    'confirmed' => 'bg-primary',
                                    'active' => 'bg-info text-dark',
                                    'pending' => 'bg-warning text-dark',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-secondary',
                                };

                                $paymentClass = match($booking->payment_status ?? 'unpaid') {
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-warning text-dark',
                                    'unpaid' => 'bg-danger',
                                    default => 'bg-secondary',
                                };

                                $vehicleName = trim(($booking->vehicle->brand ?? '') . ' ' . ($booking->vehicle->model ?? ''));
                            @endphp

                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $booking->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $booking->user->email ?? '-' }}</small>
                                </td>
                                <td>{{ $vehicleName ?: 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y, h:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y, h:i A') }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $paymentClass }}">
                                        {{ ucfirst($booking->payment_status ?? 'unpaid') }}
                                    </span>
                                </td>
                                <td>NPR {{ number_format($booking->total_price ?? 0, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('vendor.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-dark">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center py-4">No recent bookings yet.</p>
        @endif
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Available Drivers</h5>
                    <a href="{{ route('vendor.drivers.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                </div>

                @if(($availableDrivers ?? collect())->count())
                    <div class="list-group list-group-flush">
                        @foreach($availableDrivers as $driver)
                            <a href="{{ route('vendor.drivers.show', $driver->id) }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $driver->name }}</div>
                                    <small class="text-muted">{{ $driver->phone ?? 'No phone' }}</small>
                                </div>
                                <span class="badge bg-success">Available</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No available drivers right now.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Recent Customers</h5>
                    <a href="{{ route('vendor.users.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                </div>

                @if(($recentCustomers ?? collect())->count())
                    <div class="list-group list-group-flush">
                        @foreach($recentCustomers as $customer)
                            <a href="{{ route('vendor.users.show', $customer->id) }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $customer->name }}</div>
                                    <small class="text-muted">{{ $customer->email }}</small>
                                </div>
                                <span class="badge bg-primary">Customer</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No recent customers yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
