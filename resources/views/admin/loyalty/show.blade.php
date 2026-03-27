@extends('admin.layouts.master')

@section('title', 'Loyalty Details')

@section('admin-content')
<div class="mb-4">
    <a href="{{ route('admin.loyalty.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Loyalty
    </a>
    <h2 class="fw-bold mb-1">Loyalty Details</h2>
    <p class="text-muted mb-0">User loyalty account summary and transaction history</p>
</div>

@php
    $tier = $user->loyaltyAccount->tier ?? 'bronze';
    $tierConfig = match($tier) {
        'gold' => ['icon' => 'fa-crown', 'gradient' => 'linear-gradient(135deg, #f59e0b, #d97706)', 'color' => '#fff'],
        'silver' => ['icon' => 'fa-medal', 'gradient' => 'linear-gradient(135deg, #9ca3af, #6b7280)', 'color' => '#fff'],
        default => ['icon' => 'fa-shield', 'gradient' => 'linear-gradient(135deg, #a78b5e, #8b6f3c)', 'color' => '#fff'],
    };
@endphp

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width: 70px; height: 70px; background: {{ $tierConfig['gradient'] }}; color: {{ $tierConfig['color'] }};">
                    <i class="fa-solid {{ $tierConfig['icon'] }} fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-0 text-capitalize">{{ $tier }}</h5>
                <div class="text-muted small">Current Tier</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Available Points</div>
                <h3 class="fw-bold text-primary mb-0">{{ $user->loyaltyAccount->available_points ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Lifetime Earned</div>
                <h3 class="fw-bold text-success mb-0">{{ $user->loyaltyAccount->lifetime_earned_points ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Lifetime Redeemed</div>
                <h3 class="fw-bold text-danger mb-0">{{ $user->loyaltyAccount->lifetime_redeemed_points ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-user me-2 text-primary"></i>User Profile</h5>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; background: linear-gradient(135deg, #e0d4c3, #c9b896); color: #5c4a2f; font-size: 1.4rem; font-weight: 700;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                        <div class="text-muted small">{{ $user->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Quick Info</h5>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Completed Trips</span>
                    <span class="fw-bold">{{ $user->loyaltyAccount->completed_bookings_count ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">Yearly Spend</span>
                    <span class="fw-bold">Rs. {{ number_format($user->loyaltyAccount->yearly_spend ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="fw-bold mb-3">
            <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i> Transaction History
        </h5>

        @if($transactions->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fa-solid fa-receipt fa-2x mb-2 d-block opacity-50"></i>
                No loyalty transactions found.
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Booking</th>
                            <th>Amount</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            @php
                                $label = match($transaction->type) {
                                    'earn_booking' => 'Trip Completed',
                                    'earn_first_booking_bonus' => 'First Booking Bonus',
                                    'earn_review_bonus' => 'Review Bonus',
                                    'redeem' => 'Redeemed',
                                    'restore_redemption' => 'Restored',
                                    'manual_adjustment' => 'Adjustment',
                                    default => ucfirst(str_replace('_', ' ', $transaction->type)),
                                };
                                $typeClass = str_starts_with($transaction->type, 'earn') || $transaction->type === 'restore_redemption'
                                    ? 'text-bg-success' : 'text-bg-danger';
                            @endphp
                            <tr>
                                <td class="small">{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                <td><span class="badge {{ $typeClass }} rounded-pill">{{ $label }}</span></td>
                                <td>{{ $transaction->booking_id ? '#'.$transaction->booking_id : '—' }}</td>
                                <td>Rs. {{ number_format($transaction->amount_npr ?? 0, 2) }}</td>
                                <td class="fw-bold {{ $transaction->points >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
