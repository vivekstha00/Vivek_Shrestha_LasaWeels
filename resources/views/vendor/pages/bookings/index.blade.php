@extends('vendor.layouts.master')

@section('title', 'Bookings')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Bookings</h2>
    <p class="text-muted mb-0">View and manage customer bookings</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <div class="small text-muted fw-semibold text-uppercase mb-1">Coming in next 24 hours</div>
                <div class="fs-2 fw-bold text-primary">{{ $bookingMonitorSummary['upcoming'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <div class="small text-muted fw-semibold text-uppercase mb-1">Currently on trip</div>
                <div class="fs-2 fw-bold text-success">{{ $bookingMonitorSummary['on_trip'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 border-start border-danger border-4">
            <div class="card-body">
                <div class="small text-muted fw-semibold text-uppercase mb-1">Likely no-show (30+ min late)</div>
                <div class="fs-2 fw-bold text-danger">{{ $bookingMonitorSummary['missed_arrival'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-primary bg-opacity-10 text-primary fw-bold py-3 border-0">Upcoming (next 24h)</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse(($upcomingBookings ?? collect()) as $booking)
                        @php
                            $vehicleName = trim(($booking->vehicle?->brand ?? '') . ' ' . ($booking->vehicle?->model ?? ''));
                        @endphp
                        <a href="{{ route('vendor.bookings.show', $booking->id) }}" class="list-group-item list-group-item-action py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark">#{{ $booking->id }} - {{ $booking->user?->name ?? 'N/A' }}</span>
                                <span class="badge bg-primary rounded-pill">View</span>
                            </div>
                            <div class="small text-muted mb-1">{{ $vehicleName ?: ($booking->vehicle?->title ?? 'N/A') }}</div>
                            <div class="small text-dark fw-medium"><i class="fas fa-clock me-1 text-primary"></i>Pickup: {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M, h:i A') }}</div>
                        </a>
                    @empty
                        <div class="text-muted p-4 text-center">No upcoming bookings in next 24 hours.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-success bg-opacity-10 text-success fw-bold py-3 border-0">Currently on trip</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse(($currentTrips ?? collect()) as $booking)
                        @php
                            $vehicleName = trim(($booking->vehicle?->brand ?? '') . ' ' . ($booking->vehicle?->model ?? ''));
                        @endphp
                        <a href="{{ route('vendor.bookings.show', $booking->id) }}" class="list-group-item list-group-item-action py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark">#{{ $booking->id }} - {{ $booking->user?->name ?? 'N/A' }}</span>
                                <span class="badge bg-success rounded-pill">View</span>
                            </div>
                            <div class="small text-muted mb-1">{{ $vehicleName ?: ($booking->vehicle?->title ?? 'N/A') }}</div>
                            <div class="small text-dark fw-medium"><i class="fas fa-flag-checkered me-1 text-success"></i>Drop: {{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M, h:i A') }}</div>
                        </a>
                    @empty
                        <div class="text-muted p-4 text-center">No user is currently on trip.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold py-3 border-0">Likely no-show</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse(($missedArrivalBookings ?? collect()) as $booking)
                        @php
                            $vehicleName = trim(($booking->vehicle?->brand ?? '') . ' ' . ($booking->vehicle?->model ?? ''));
                        @endphp
                        <a href="{{ route('vendor.bookings.show', $booking->id) }}" class="list-group-item list-group-item-action py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark">#{{ $booking->id }} - {{ $booking->user?->name ?? 'N/A' }}</span>
                                <span class="badge bg-danger rounded-pill">View</span>
                            </div>
                            <div class="small text-muted mb-1">{{ $vehicleName ?: ($booking->vehicle?->title ?? 'N/A') }}</div>
                            <div class="small text-danger fw-medium"><i class="fas fa-exclamation-circle me-1"></i>Pickup was: {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M, h:i A') }}</div>
                        </a>
                    @empty
                        <div class="text-muted p-4 text-center">No likely no-show bookings right now.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vehicle</th>
                        <th>User</th>
                        <th>Pickup</th>
                        <th>Drop</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr style="cursor: pointer;"
                            onclick="window.location='{{ route('vendor.bookings.show', $booking->id) }}'">
                            <td><strong>#{{ $booking->id }}</strong></td>
                            <td>{{ $booking->vehicle?->title ?? $booking->vehicle?->brand ?? 'N/A' }}</td>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y') }}</td>
                            <td>
                                <span class="badge
                                    {{ $booking->status === 'pending' ? 'bg-warning' :
                                       ($booking->status === 'confirmed' ? 'bg-primary' : 'bg-success') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge
                                    {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td class="fw-medium">Rs. {{ number_format($booking->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No bookings found yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
