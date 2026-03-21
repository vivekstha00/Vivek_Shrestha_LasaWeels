@extends('admin.layouts.master')

@section('title', 'Loyalty Details')

@section('admin-content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Loyalty Details</h3>
            <p class="text-muted mb-0">User loyalty account summary and transaction history</p>
        </div>

        <a href="{{ route('admin.loyalty.index') }}" class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">User Information</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Name</div>
                    <div class="fw-semibold">{{ $user->name }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Email</div>
                    <div class="fw-semibold">{{ $user->email }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Tier</div>
                    <h4 class="fw-bold mb-0">{{ ucfirst($user->loyaltyAccount->tier ?? 'bronze') }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Available Points</div>
                    <h4 class="fw-bold text-primary mb-0">{{ $user->loyaltyAccount->available_points ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Lifetime Earned</div>
                    <h4 class="fw-bold text-success mb-0">{{ $user->loyaltyAccount->lifetime_earned_points ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Lifetime Redeemed</div>
                    <h4 class="fw-bold text-danger mb-0">{{ $user->loyaltyAccount->lifetime_redeemed_points ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Loyalty Transaction History</h5>

            @if($transactions->isEmpty())
                <div class="text-center text-muted py-5">
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
                                @endphp
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                    <td>{{ $label }}</td>
                                    <td>{{ $transaction->booking_id ? '#'.$transaction->booking_id : '-' }}</td>
                                    <td>Rs. {{ number_format($transaction->amount_npr ?? 0, 2) }}</td>
                                    <td class="fw-semibold {{ $transaction->points >= 0 ? 'text-success' : 'text-danger' }}">
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
</div>
@endsection
