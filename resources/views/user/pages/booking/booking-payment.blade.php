@extends('user.layouts.master')

@section('title', 'Booking Payment')

@section('user-content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Header --}}
            <div class="text-center mb-4">
                <h2 class="fw-bold">Complete Your Payment</h2>
                <p class="text-muted">Choose your payment method to confirm the booking</p>
            </div>

            {{-- Booking Summary --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="fw-bold mb-2">Booking #{{ $booking->id }}</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Vehicle:</strong> {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                                    <p class="mb-1"><strong>Service:</strong> {{ $booking->service === 'driver' ? 'With Driver' : 'Self Drive' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Pickup:</strong> {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('M d, Y H:i') }}</p>
                                    <p class="mb-1"><strong>Drop:</strong> {{ \Carbon\Carbon::parse($booking->drop_datetime)->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="fs-2 fw-bold text-success">Rs. {{ number_format($booking->total_price, 2) }}</div>
                            <small class="text-muted">Total Amount</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Options --}}
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 border-primary">
                        <div class="card-body text-center p-4">
                            <h5 class="fw-bold mb-2">Full Online Payment</h5>
                            <p class="text-muted mb-3">Pay the complete amount now using Khalti</p>
                            <div class="fs-4 fw-bold text-primary mb-3">Rs. {{ number_format($booking->total_price, 2) }}</div>
                            <form method="POST" action="{{ route('booking.payment.process', $booking->id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="payment_option" value="full_online">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    Pay Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-success">
                        <div class="card-body text-center p-4">
                            <h5 class="fw-bold mb-2">Deposit + Cash</h5>
                            <p class="text-muted mb-3">Pay 20% now, remaining 80% in cash at pickup</p>
                            <div class="mb-2">
                                <div class="fs-5 fw-bold text-success">Deposit: Rs. {{ number_format($booking->total_price * 0.2, 2) }}</div>
                                <small class="text-muted">Balance: Rs. {{ number_format($booking->total_price * 0.8, 2) }} (at pickup)</small>
                            </div>
                            <form method="POST" action="{{ route('booking.payment.process', $booking->id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="payment_option" value="deposit_cash">
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    Pay Deposit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terms & Policy --}}
            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Cancellation Policy</h6>
                            <p class="small mb-0">Free cancellation up to 24 hours before pickup. Late cancellation or no-show may incur charges.</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Payment Security</h6>
                            <p class="small mb-0">All payments are processed securely through Khalti. Your payment information is protected.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Errors --}}
            @if($errors->any())
                <div class="alert alert-danger mt-3">
                    {{ $errors->first() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
