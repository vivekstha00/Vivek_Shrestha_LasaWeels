@extends('admin.layouts.master')

@section('title', 'Loyalty Management')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Loyalty Management</h2>
    <p class="text-muted">Monitor user points, tiers, and redemption activity</p>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Loyalty Accounts</div>
                <h3 class="fw-bold">{{ $counts['accounts'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Available Points</div>
                <h3 class="fw-bold text-primary">{{ $counts['available_points'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Lifetime Earned</div>
                <h3 class="fw-bold text-success">{{ $counts['earned_points'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Lifetime Redeemed</div>
                <h3 class="fw-bold text-danger">{{ $counts['redeemed_points'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by user name or email">
            </div>
            <div class="col-md-4">
                <select name="tier" class="form-select">
                    <option value="">All Tiers</option>
                    <option value="bronze" {{ request('tier') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                    <option value="silver" {{ request('tier') == 'silver' ? 'selected' : '' }}>Silver</option>
                    <option value="gold" {{ request('tier') == 'gold' ? 'selected' : '' }}>Gold</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Loyalty Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-clickable align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Tier</th>
                    <th>Available</th>
                    <th>Earned</th>
                    <th>Redeemed</th>
                    <th>Completed Trips</th>
                    <th>Yearly Spend</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr onclick="window.location='{{ route('admin.loyalty.show', $account->user_id) }}'">
                        <td>
                            <div class="fw-semibold">{{ $account->user->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $account->user->email ?? '' }}</small>
                        </td>
                        <td>
                            @php
                                $tierClass = match($account->tier) {
                                    'gold' => 'bg-warning text-dark',
                                    'silver' => 'bg-secondary',
                                    default => 'bg-dark bg-opacity-25 text-dark'
                                };
                            @endphp
                            <span class="badge {{ $tierClass }}">{{ ucfirst($account->tier) }}</span>
                        </td>
                        <td class="fw-bold text-primary">{{ $account->available_points }}</td>
                        <td class="fw-bold text-success">{{ $account->lifetime_earned_points }}</td>
                        <td class="fw-bold text-danger">{{ $account->lifetime_redeemed_points }}</td>
                        <td>{{ $account->completed_bookings_count ?? 0 }}</td>
                        <td>Rs. {{ number_format($account->yearly_spend ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No loyalty accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $accounts->links() }}
    </div>
</div>
@endsection
