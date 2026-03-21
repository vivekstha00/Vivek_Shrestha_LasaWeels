@extends('vendor.layouts.master')

@section('title', 'Booking Details')

@section('vendor-content')
<div class="container-fluid">

    @php
        $originalAmount = (float) $booking->total_price + (float) ($booking->loyalty_discount_amount ?? 0);
        $loyaltyDiscount = (float) ($booking->loyalty_discount_amount ?? 0);
        $finalCustomerPayment = (float) $booking->total_price;
    @endphp

    <h4 class="mb-4">Booking Details #{{ $booking->id }}</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="row">

                <div class="col-md-6">
                    <h5>Customer Info</h5>
                    <p><strong>Name:</strong> {{ $booking->user->name }}</p>
                    <p><strong>Email:</strong> {{ $booking->user->email }}</p>
                    <p><strong>Phone:</strong> {{ $booking->user->phone }}</p>
                </div>

                <div class="col-md-6">
                    <h5>Vehicle Info</h5>
                    <p><strong>Vehicle:</strong> {{ $booking->vehicle->title }}</p>
                    <p><strong>Service:</strong> {{ ucfirst($booking->service) }}</p>
                </div>

            </div>

            <hr>

            <div class="row mt-3">

                <div class="col-md-6">
                    <p><strong>Pickup Location:</strong> {{ $booking->pickup_location }}</p>
                    <p><strong>Drop Location:</strong> {{ $booking->drop_location }}</p>
                </div>

                <div class="col-md-6">
                    <p><strong>Pickup Date:</strong>
                        {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y h:i A') }}
                    </p>
                    <p><strong>Drop Date:</strong>
                        {{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y h:i A') }}
                    </p>
                </div>

            </div>

            <hr>

            <p><strong>Status:</strong>
                <span class="badge bg-warning">
                    {{ ucfirst($booking->status) }}
                </span>
            </p>

            <p><strong>Payment Status:</strong>
                <span class="badge
                    {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($booking->payment_status) }}
                </span>
            </p>

            <div class="card border-0 bg-light rounded-4 mt-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Pricing Breakdown</h5>

                    <p class="mb-2">
                        <strong>Original Booking Amount:</strong>
                        Rs. {{ number_format($originalAmount, 2) }}
                    </p>

                    <p class="mb-2">
                        <strong>Loyalty Discount:</strong>
                        <span class="text-danger">
                            - Rs. {{ number_format($loyaltyDiscount, 2) }}
                        </span>
                    </p>

                    <p class="mb-2">
                        <strong>Final Customer Payment:</strong>
                        <span class="text-success">
                            Rs. {{ number_format($finalCustomerPayment, 2) }}
                        </span>
                    </p>

                    @if($booking->payment)
                        <hr>
                        <p class="mb-2">
                            <strong>Platform Commission:</strong>
                            Rs. {{ number_format($booking->payment->platform_commission ?? 0, 2) }}
                        </p>

                        <p class="mb-0">
                            <strong>Your Net Amount:</strong>
                            Rs. {{ number_format($booking->payment->vendor_amount ?? 0, 2) }}
                        </p>
                    @endif

                    @if($loyaltyDiscount > 0)
                        <div class="alert alert-info mt-3 mb-0">
                            Loyalty discount is platform-funded and does not reduce your payout basis.
                        </div>
                    @endif
                </div>
            </div>

            <a href="{{ route('vendor.bookings.index') }}"
               class="btn btn-secondary mt-3">
               Back
            </a>

        </div>
    </div>

</div>
@endsection
