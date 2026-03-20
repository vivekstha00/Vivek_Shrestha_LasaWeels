@extends('user.layouts.master')

@section('user-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Loyalty Activity</h3>
            <p class="text-muted mb-0">View all your loyalty points history</p>
        </div>
        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary rounded-3">
            Back to Profile
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="border rounded-4 p-3 text-center">
                        <div class="small text-muted mb-1">Available Points</div>
                        <h4 class="fw-bold text-primary mb-0">{{ $loyaltyAccount->available_points ?? 0 }}</h4>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="border rounded-4 p-3 text-center">
                        <div class="small text-muted mb-1">Lifetime Earned</div>
                        <h4 class="fw-bold text-success mb-0">{{ $loyaltyAccount->lifetime_earned_points ?? 0 }}</h4>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="border rounded-4 p-3 text-center">
                        <div class="small text-muted mb-1">Redeemed</div>
                        <h4 class="fw-bold text-danger mb-0">{{ $loyaltyAccount->lifetime_redeemed_points ?? 0 }}</h4>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="border rounded-4 p-3 text-center">
                        <div class="small text-muted mb-1">Tier</div>
                        <h4 class="fw-bold mb-0">{{ ucfirst($loyaltyAccount->tier ?? 'bronze') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="mb-0 fw-bold">All Loyalty Transactions</h5>
        </div>

        <div class="card-body px-4 pb-4">
            @if($loyaltyTransactions->isEmpty())
                <div class="text-center py-5">
                    <h6 class="fw-bold mb-2">No loyalty activity found</h6>
                    <p class="text-muted mb-0">Your points history will appear here.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Activity</th>
                                <th>Booking</th>
                                <th>Amount</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loyaltyTransactions as $transaction)
                                @php
                                    $typeLabel = match($transaction->type) {
                                        'earn_booking' => 'Trip Completed',
                                        'earn_first_booking_bonus' => 'First Booking Bonus',
                                        'earn_review_bonus' => 'Review Bonus',
                                        'redeem' => 'Redeemed',
                                        'restore_redemption' => 'Restored',
                                        'manual_adjustment' => 'Adjustment',
                                        default => ucfirst(str_replace('_', ' ', $transaction->type)),
                                    };

                                    $pointClass = $transaction->points >= 0 ? 'text-success' : 'text-danger';
                                @endphp

                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                    <td>{{ $typeLabel }}</td>
                                    <td>{{ $transaction->booking_id ? '#'.$transaction->booking_id : '-' }}</td>
                                    <td>NPR {{ number_format($transaction->amount_npr ?? 0, 2) }}</td>
                                    <td class="fw-bold {{ $pointClass }}">
                                        {{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $loyaltyTransactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
