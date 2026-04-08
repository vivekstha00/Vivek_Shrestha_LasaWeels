@extends('vendor.layouts.master')

@section('title', 'Reports')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Vendor Reports Dashboard</h2>
    <p class="text-muted mb-0">Minimal KPIs for bookings, earnings, availability, payments, and ratings</p>
</div>

{{-- Date Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('vendor.reports.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">From</label>
                <input type="date" name="from_date" class="form-control" value="{{ $filters['from_date'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To</label>
                <input type="date" name="to_date" class="form-control" value="{{ $filters['to_date'] ?? '' }}">
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('vendor.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Total Bookings</small>
                <h4 class="fw-bold">{{ $totalBookings }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Total Revenue</small>
                <h4 class="fw-bold">Rs. {{ number_format($totalRevenue, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Payout Ready</small>
                <h4 class="fw-bold text-info">Rs. {{ number_format($payoutReadyAmount, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Average Rating</small>
                <h4 class="fw-bold text-primary">{{ number_format((float) $averageRating, 1) }}</h4>
                <small class="text-muted">{{ $totalReviews }} reviews</small>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-4 mb-4">

    {{-- Booking Status Doughnut --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Booking Status Summary</h6>
                <canvas id="vendorBookingStatusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Earnings by Vehicle --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Earnings by Vehicle</h6>
                @forelse($earningsByVehicle as $row)
                    @php
                        $vehicle = $row->vehicle;
                        $label   = $vehicle
                            ? ($vehicle->title ?? $vehicle->vehicle_name ?? $vehicle->plate_number ?? 'Vehicle #' . $row->vehicle_id)
                            : 'Vehicle #' . $row->vehicle_id;
                        $pct = $maxVehicleEarning > 0
                            ? round(($row->total_earnings / $maxVehicleEarning) * 100)
                            : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-semibold text-truncate" style="max-width:55%">{{ $label }}</span>
                            <span class="small text-muted">
                                Rs.{{ number_format($row->total_earnings, 0) }}
                                &middot; {{ $row->total_bookings }} trip{{ $row->total_bookings != 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No earnings data available.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Vehicle Availability --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Vehicle Availability</h6>
                <canvas id="vendorVehicleAvailabilityChart"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- Payment Summary --}}
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Payment Summary</h6>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Paid Payments</span>
                    <span class="fw-semibold text-success">{{ $paymentSummary['paid'] }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Pending Payments</span>
                    <span class="fw-semibold text-warning">{{ $paymentSummary['pending'] }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Refunded Payments</span>
                    <span class="fw-semibold text-danger">{{ $paymentSummary['refunded'] }}</span>
                </div>
                <div class="d-flex justify-content-between pt-3">
                    <span class="text-muted">Total Received</span>
                    <span class="fw-bold">Rs. {{ number_format($paymentSummary['received'], 2) }}</span>
                </div>
                <div class="small text-muted mt-3">
                    Paid out: Rs. {{ number_format($paidAmount, 2) }}
                    &middot; Pending payout: Rs. {{ number_format($pendingPayoutAmount, 2) }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookingStatusChartData       = @json($bookingStatusChart);
        const vehicleAvailabilityChartData = @json($vehicleAvailabilityChart);

        // ── Booking Status Doughnut ───────────────────────────────────────────
        new Chart(document.getElementById('vendorBookingStatusChart'), {
            type: 'doughnut',
            data: {
                labels: bookingStatusChartData.labels,
                datasets: [{
                    data: bookingStatusChartData.data,
                    backgroundColor: ['#f59e0b', '#3b82f6', '#06b6d4', '#22c55e', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // ── Vehicle Availability Bar ──────────────────────────────────────────
        new Chart(document.getElementById('vendorVehicleAvailabilityChart'), {
            type: 'bar',
            data: {
                labels: vehicleAvailabilityChartData.labels,
                datasets: [{
                    data: vehicleAvailabilityChartData.data,
                    backgroundColor: ['#22c55e', '#3b82f6', '#f97316']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } }
                }
            }
        });
    });
</script>
@endpush
