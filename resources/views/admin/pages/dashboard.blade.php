@extends('admin.layouts.master')

@section('admin-content')
<div class="mb-3">
    <h2 class="fw-bold mb-1">Platform Overview</h2>
    <p class="text-muted mb-0">Monitor and manage the entire platform</p>
</div>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total Users</small>
                <h3 class="fw-bold mb-0">{{ $statistics['totalUsers'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total Vendors</small>
                <h3 class="fw-bold mb-0">{{ $statistics['totalVendors'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total Vehicles</small>
                <h3 class="fw-bold mb-0">{{ $statistics['totalVehicles'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Pending Approvals</small>
                <h3 class="fw-bold mb-0">
                    {{ ($statistics['pendingVendors'] ?? 0) + ($statistics['pendingVehicles'] ?? 0) + ($statistics['pendingDocs'] ?? 0) }}
                </h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-bold bg-white">Pending Approvals</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="border rounded p-3">
                    <div class="text-muted small">Vendors</div>
                    <div class="fw-bold fs-4">{{ $statistics['pendingVendors'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3">
                    <div class="text-muted small">Vehicles</div>
                    <div class="fw-bold fs-4">{{ $statistics['pendingVehicles'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3">
                    <div class="text-muted small">Documents</div>
                    <div class="fw-bold fs-4">{{ $statistics['pendingDocs'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-bold bg-white">Recent Users</div>
    <div class="table-responsive">
        <table class="table mb-0">
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
                        <td colspan="2" class="text-center text-muted py-3">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">
    <h4 class="fw-bold mb-3">Loyalty Analytics</h4>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Loyalty Accounts</small>
                    <h3 class="fw-bold mb-0">{{ $statistics['loyaltyAccounts'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Available Points</small>
                    <h3 class="fw-bold text-primary mb-0">{{ $statistics['availablePoints'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Lifetime Earned</small>
                    <h3 class="fw-bold text-success mb-0">{{ $statistics['earnedPoints'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Lifetime Redeemed</small>
                    <h3 class="fw-bold text-danger mb-0">{{ $statistics['redeemedPoints'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Total Loyalty Discount Given</small>
                    <h3 class="fw-bold mb-0">Rs. {{ number_format($statistics['totalLoyaltyDiscount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold bg-white">Top Loyalty Users</div>
            <div class="table-responsive">
                <table class="table mb-0">
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
                                <td>{{ ucfirst($account->tier) }}</td>
                                <td class="fw-bold text-primary">{{ $account->available_points }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No loyalty data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold bg-white">Recent Loyalty Activity</div>
            <div class="table-responsive">
                <table class="table mb-0">
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
                                    'earn_review_bonus' => 'Review Bonus',
                                    'redeem' => 'Redeemed',
                                    'restore_redemption' => 'Restored',
                                    'manual_adjustment' => 'Adjustment',
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
                                <td colspan="3" class="text-center text-muted py-3">No recent loyalty transactions</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
