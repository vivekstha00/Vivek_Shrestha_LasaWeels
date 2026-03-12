@extends('user.layouts.master')

@section('title', 'Booking Payment')

@section('user-content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">Complete Your Payment</h3>

                    <div class="mb-4">
                        <p class="mb-1"><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                        <p class="mb-1"><strong>Total Amount:</strong> Rs. {{ number_format($booking->total_price, 2) }}</p>
                        <p class="mb-0 text-muted">
                            Choose your payment option to confirm the booking.
                        </p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('booking.payment.process', $booking->id) }}">
                        @csrf

                        <div class="border rounded-3 p-3 mb-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="payment_option"
                                    id="full_online"
                                    value="full_online"
                                    checked
                                >
                                <label class="form-check-label w-100" for="full_online">
                                    <strong>Full Online Payment</strong><br>
                                    <small class="text-muted">
                                        Pay the full amount now using Khalti.
                                    </small>
                                </label>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 mb-4">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="payment_option"
                                    id="deposit_cash"
                                    value="deposit_cash"
                                >
                                <label class="form-check-label w-100" for="deposit_cash">
                                    <strong>20% Deposit + Remaining Cash</strong><br>
                                    <small class="text-muted">
                                        Pay 20% now to confirm your booking. Remaining 80% will be paid in cash at pickup.
                                    </small>
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Cancellation Policy:</strong><br>
                            Deposit is refundable only if you cancel more than 24 hours before pickup. Late cancellation or no-show will make the deposit non-refundable.
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            Continue Payment
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
