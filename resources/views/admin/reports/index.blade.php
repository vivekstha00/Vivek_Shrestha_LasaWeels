@extends('admin.layouts.master')

@section('title', 'Reports')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Reports Dashboard</h2>
    <p class="text-muted mb-0">Core operational KPIs with concise visual trends</p>
</div>

{{-- Date Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
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
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                <small class="text-muted">Gross Revenue</small>
                <h4 class="fw-bold">Rs. {{ number_format($grossBookingRevenue, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Platform Commission</small>
                <h4 class="fw-bold text-primary">Rs. {{ number_format($platformCommission, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Vendor Earnings</small>
                <h4 class="fw-bold text-success">Rs. {{ number_format($vendorEarnings, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Overall Booking Status</h6>
                <canvas id="bookingStatusChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Top Booked Vehicles</h6>
                <canvas id="topVehiclesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Revenue vs Commission</h6>
                <canvas id="revenueComparisonChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Vendor Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Total Vendors</small>
                <h4 class="fw-bold">{{ $totalVendors }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Active Vendors</small>
                <h4 class="fw-bold text-success">{{ $activeVendors }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Pending / Inactive Vendors</small>
                <h4 class="fw-bold text-warning">{{ $pendingVendors + $inactiveVendors }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- Subscription Summary --}}
<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Subscription Summary</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <small class="text-muted">Active Subscriptions</small>
                    <div class="fw-bold text-success">{{ $activeSubscriptions }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <small class="text-muted">Expired Subscriptions</small>
                    <div class="fw-bold text-danger">{{ $expiredSubscriptions }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <small class="text-muted">Monthly Plans</small>
                    <div class="fw-bold">{{ $monthlyPlans }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <small class="text-muted">Yearly Plans</small>
                    <div class="fw-bold">{{ $yearlyPlans }}</div>
                </div>
            </div>
        </div>
        <div class="mt-3 fw-semibold">
            Subscription Revenue: Rs. {{ number_format($subscriptionRevenueTotal, 2) }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookingStatusChartData     = @json($bookingStatusChart);
        const topVehiclesChartData       = @json($topVehiclesChart);
        const revenueComparisonChartData = @json($revenueComparisonChart);

        // ── Booking Status Doughnut ───────────────────────────────────────────
        new Chart(document.getElementById('bookingStatusChart'), {
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

        // ── Top Booked Vehicles Horizontal Bar ────────────────────────────────
        new Chart(document.getElementById('topVehiclesChart'), {
            type: 'bar',
            data: {
                labels: topVehiclesChartData.labels,
                datasets: [{
                    label: 'Bookings',
                    data: topVehiclesChartData.data,
                    backgroundColor: [
                        '#3b82f6', '#06b6d4', '#22c55e', '#f59e0b',
                        '#a855f7', '#ef4444', '#0ea5e9'
                    ],
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.x} booking${ctx.parsed.x !== 1 ? 's' : ''}`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    },
                    y: {
                        ticks: {
                            callback: function (val) {
                                const label = this.getLabelForValue(val);
                                return label.length > 20 ? label.slice(0, 18) + '…' : label;
                            }
                        }
                    }
                }
            }
        });

        // ── Revenue vs Commission Bar ─────────────────────────────────────────
        new Chart(document.getElementById('revenueComparisonChart'), {
            type: 'bar',
            data: {
                labels: revenueComparisonChartData.labels,
                datasets: [{
                    label: 'Amount (Rs.)',
                    data: revenueComparisonChartData.data,
                    backgroundColor: ['#0ea5e9', '#22c55e', '#a855f7']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => {
                                if (value >= 1000000) return 'Rs.' + (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000)    return 'Rs.' + (value / 1000).toFixed(0) + 'K';
                                return 'Rs.' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
