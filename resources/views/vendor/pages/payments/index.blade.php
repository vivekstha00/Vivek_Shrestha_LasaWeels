@extends('vendor.layouts.master')

@section('title', 'My Payments')

@section('vendor-content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Payments</h3>
            <p class="text-muted mb-0">Track earnings, commission, and payout status</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Total Gross Revenue</small>
                    <h4 class="fw-bold mb-0">Rs. {{ number_format($totalGross, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Total Commission</small>
                    <h4 class="fw-bold mb-0">Rs. {{ number_format($totalCommission, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Net Earnings</small>
                    <h4 class="fw-bold mb-0">Rs. {{ number_format($totalNet, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Paid Payouts</small>
                    <h4 class="fw-bold mb-0">Rs. {{ number_format($paidPayout, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Pending Payouts</small>
                    <h4 class="fw-bold mb-0">Rs. {{ number_format($pendingPayout, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">#</th>
                        <th>Booking</th>
                        <th>Customer</th>
                        <th>Payment Type</th>
                        <th>Total</th>
                        <th>Commission</th>
                        <th>Net Amount</th>
                        <th>Payout</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr
                            onclick="window.location='{{ route('vendor.payments.show', $payment->id) }}'"
                            style="cursor:pointer;"
                            class="vendor-payment-row"
                        >
                            <td class="px-4 fw-semibold">#{{ $payment->id }}</td>
                            <td>#{{ $payment->booking_id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $payment->user->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $payment->user->email ?? '' }}</small>
                            </td>
                            <td>
                                @if($payment->payment_type === 'deposit_cash')
                                    <span class="badge text-bg-info rounded-pill px-3 py-2">Deposit + Cash</span>
                                @else
                                    <span class="badge text-bg-primary rounded-pill px-3 py-2">Full Online</span>
                                @endif
                            </td>
                            <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                            <td>Rs. {{ number_format($payment->platform_commission, 2) }}</td>
                            <td>Rs. {{ number_format($payment->vendor_amount, 2) }}</td>
                            <td>
                                @if($payment->payout_status === 'paid')
                                    <span class="badge text-bg-success rounded-pill px-3 py-2">Paid</span>
                                @elseif($payment->payout_status === 'pending')
                                    <span class="badge text-bg-warning rounded-pill px-3 py-2">Pending</span>
                                @else
                                    <span class="badge text-bg-secondary rounded-pill px-3 py-2">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">No payment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>

</div>

<style>
    .vendor-payment-row:hover {
        background-color: #f8f9fa;
        transition: 0.2s ease;
    }
</style>
@endsection
