@extends('vendor.layouts.master')
@section('title', 'Vendor Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview of your vehicle rental business')

@section('vendor-content')
<div class="container-fluid">

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Total Vehicles</div>
                    <div class="fs-3 fw-bold">{{ $totalVehicles ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Active Bookings</div>
                    <div class="fs-3 fw-bold">{{ $activeBookings ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Monthly Revenue</div>
                    <div class="fs-3 fw-bold">{{ $monthlyRevenue ?? '—' }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-soft border-start border-4 border-warning">
                <div class="card-body">
                    <div class="text-muted small">Pending Requests</div>
                    <div class="fs-3 fw-bold">{{ $pendingVehicles ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Original Booking Value</div>
                    <div class="fs-4 fw-bold">Rs. {{ number_format($originalBookingValue ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Loyalty Discount Impact</div>
                    <div class="fs-4 fw-bold text-danger">Rs. {{ number_format($totalLoyaltyDiscount ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Customer Paid</div>
                    <div class="fs-4 fw-bold">Rs. {{ number_format($totalCustomerPaid ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Net Earnings</div>
                    <div class="fs-4 fw-bold">Rs. {{ number_format($totalNet ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Platform Commission</div>
                    <div class="fs-4 fw-bold">Rs. {{ number_format($totalCommission ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted small">Bookings Using Loyalty Discount</div>
                    <div class="fs-4 fw-bold">{{ $discountedBookingsCount ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-soft table-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Recent Vehicle Submissions</h6>
                <a class="small" href="{{ route('vendor.vehicles.index') }}">View all</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>City</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($recentVehicles ?? []) as $v)
                            <tr>
                                <td class="fw-semibold">{{ $v->title }}</td>
                                <td>{{ $v->vehicle_type }}</td>
                                <td>{{ $v->location_city }}</td>
                                <td>{{ number_format($v->price_per_day,2) }} {{ $v->currency }}</td>
                                <td>
                                    @if($v->status === 'pending')
                                        <span class="badge badge-soft badge-pending">Pending</span>
                                    @elseif($v->status === 'approved')
                                        <span class="badge badge-soft badge-approved">Approved</span>
                                    @else
                                        <span class="badge badge-soft badge-rejected">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted py-3">No recent records.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('vendor.vehicles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Vehicle
                </a>
                <button class="btn btn-outline-secondary" disabled>View Reports</button>
            </div>
        </div>
    </div>

</div>
@endsection
