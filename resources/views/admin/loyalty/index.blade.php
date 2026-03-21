@extends('admin.layouts.master')

@section('title', 'Loyalty Management')

@section('admin-content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Loyalty Management</h3>
            <p class="text-muted mb-0">Monitor user points, tiers, and redemption activity</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Loyalty Accounts</div>
                    <h3 class="fw-bold mb-0">{{ $counts['accounts'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Available Points</div>
                    <h3 class="fw-bold mb-0 text-primary">{{ $counts['available_points'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Lifetime Earned</div>
                    <h3 class="fw-bold mb-0 text-success">{{ $counts['earned_points'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Lifetime Redeemed</div>
                    <h3 class="fw-bold mb-0 text-danger">{{ $counts['redeemed_points'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.loyalty.index') }}" class="row g-2">
                <div class="col-lg-8">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control rounded-3"
                        placeholder="Search by user name or email"
                    >
                </div>

                <div class="col-lg-2">
                    <select name="tier" class="form-select rounded-3">
                        <option value="">All Tiers</option>
                        <option value="bronze" {{ request('tier') === 'bronze' ? 'selected' : '' }}>Bronze</option>
                        <option value="silver" {{ request('tier') === 'silver' ? 'selected' : '' }}>Silver</option>
                        <option value="gold" {{ request('tier') === 'gold' ? 'selected' : '' }}>Gold</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <button class="btn btn-primary w-100 rounded-3">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">User</th>
                        <th>Tier</th>
                        <th>Available</th>
                        <th>Earned</th>
                        <th>Redeemed</th>
                        <th>Completed Trips</th>
                        <th>Yearly Spend</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <td class="px-4">
                                <div class="fw-semibold">{{ $account->user->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $account->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge text-bg-secondary rounded-pill px-3 py-2">
                                    {{ ucfirst($account->tier) }}
                                </span>
                            </td>
                            <td class="fw-semibold text-primary">{{ $account->available_points }}</td>
                            <td class="fw-semibold text-success">{{ $account->lifetime_earned_points }}</td>
                            <td class="fw-semibold text-danger">{{ $account->lifetime_redeemed_points }}</td>
                            <td>{{ $account->completed_bookings_count }}</td>
                            <td>Rs. {{ number_format($account->yearly_spend, 2) }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.loyalty.show', $account->user_id) }}"
                                   class="btn btn-sm btn-outline-primary rounded-3">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                No loyalty accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $accounts->links() }}
    </div>
</div>
@endsection
