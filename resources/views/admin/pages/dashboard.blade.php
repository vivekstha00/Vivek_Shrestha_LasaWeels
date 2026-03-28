@extends('admin.layouts.master')

@section('title', 'Admin Dashboard')

@section('admin-content')
@php
    $pendingTotal = ($statistics['pendingVendors'] ?? 0) + ($statistics['pendingVehicles'] ?? 0) + ($statistics['pendingDocs'] ?? 0);
@endphp

<div class="mb-5 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Platform Control Center</h2>
        <p class="text-muted mb-0">Operational snapshot and priority queues for daily admin actions</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        Refresh
    </a>
</div>

<!-- Executive Snapshot -->
<div class="row g-3 mb-5">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small">Total Users</div>
                <h3 class="fw-bold mb-0">{{ $statistics['totalUsers'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small">Total Vendors</div>
                <h3 class="fw-bold mb-0">{{ $statistics['totalVendors'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small">Total Vehicles</div>
                <h3 class="fw-bold mb-0">{{ $statistics['totalVehicles'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small">Open Approval Queue</div>
                <h3 class="fw-bold text-warning mb-0">{{ $pendingTotal }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Action Required -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Action Required</h4>
        <p class="text-muted mb-0">Prioritize pending records that need approval</p>
    </div>
</div>

<div class="row g-3 mb-5">
    <div class="col-md-4">
        <a href="{{ route('admin.vendors.index') }}" class="text-decoration-none text-dark">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Pending Vendors</div>
                            <h3 class="fw-bold mb-1">{{ $statistics['pendingVendors'] ?? 0 }}</h3>
                            <span class="small text-primary">Review vendor requests</span>
                        </div>
                        <span class="badge text-bg-warning">Queue</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('admin.vehicles.index') }}" class="text-decoration-none text-dark">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Pending Vehicles</div>
                            <h3 class="fw-bold mb-1">{{ $statistics['pendingVehicles'] ?? 0 }}</h3>
                            <span class="small text-primary">Verify and approve listings</span>
                        </div>
                        <span class="badge text-bg-warning">Queue</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('admin.documents.index') }}" class="text-decoration-none text-dark">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Pending Documents</div>
                            <h3 class="fw-bold mb-1">{{ $statistics['pendingDocs'] ?? 0 }}</h3>
                            <span class="small text-primary">Validate user documents</span>
                        </div>
                        <span class="badge text-bg-warning">Queue</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Customer Funnel + Loyalty -->
<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-semibold mb-1">Newest Customers</h5>
                <p class="small text-muted mb-0">Most recent user registrations</p>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($statistics['recentUsers'] ?? []) as $user)
                            <tr>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">No recent users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-semibold mb-1">Loyalty Snapshot</h5>
                <p class="small text-muted mb-0">Point economy and discount impact</p>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="small text-muted">Loyalty Accounts</div>
                            <div class="fw-bold fs-4">{{ $statistics['loyaltyAccounts'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="small text-muted">Available Points</div>
                            <div class="fw-bold fs-4 text-primary">{{ $statistics['availablePoints'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="small text-muted">Lifetime Earned</div>
                            <div class="fw-bold fs-4 text-success">{{ $statistics['earnedPoints'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="small text-muted">Lifetime Redeemed</div>
                            <div class="fw-bold fs-4 text-danger">{{ $statistics['redeemedPoints'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded-3 p-3 text-center">
                            <div class="small text-muted">Total Loyalty Discount Given</div>
                            <div class="fw-bold fs-5">NPR {{ number_format((float) ($statistics['totalLoyaltyDiscount'] ?? 0), 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loyalty Leaderboard + Activity -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 fw-semibold">Top Loyalty Users</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Tier</th>
                            <th>Available Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($statistics['topLoyaltyUsers'] ?? []) as $account)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $account->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $account->user->email ?? '' }}</small>
                                </td>
                                <td><span class="badge bg-secondary">{{ ucfirst($account->tier) }}</span></td>
                                <td class="fw-bold text-primary">{{ $account->available_points }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No data yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 fw-semibold">Recent Loyalty Activity</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($statistics['recentLoyaltyTransactions'] ?? []) as $tx)
                            @php
                                $label = match($tx->type) {
                                    'earn_booking' => 'Trip Completed',
                                    'earn_first_booking_bonus' => 'First Booking Bonus',
                                    default => ucfirst(str_replace('_', ' ', $tx->type)),
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $tx->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $tx->created_at->format('d M Y') }}</small>
                                </td>
                                <td>{{ $label }}</td>
                                <td class="fw-bold {{ $tx->points >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->points > 0 ? '+' : '' }}{{ $tx->points }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No recent activity</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
