@extends('user.layouts.master')

@section('user-content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h4 class="fw-bold mb-4">Choose Payment Method</h4>

                    <div class="mb-3">
                        <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                        <p><strong>Total:</strong> NPR {{ number_format($booking->total_price,2) }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('booking.payment.process', $booking->id) }}">
                        @csrf

                        <div class="mb-3">
                            <div class="form-check border rounded p-3 mb-3">
                                <input class="form-check-input"
                                       type="radio"
                                       name="payment_method"
                                       value="cash"
                                       required>
                                <label class="form-check-label">
                                    Cash on Pickup
                                </label>
                            </div>

                            <div class="form-check border rounded p-3">
                                <input class="form-check-input"
                                       type="radio"
                                       name="payment_method"
                                       value="khalti">
                                <label class="form-check-label">
                                    Khalti (Coming Soon)
                                </label>
                            </div>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-success px-4">
                                Confirm Booking
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
