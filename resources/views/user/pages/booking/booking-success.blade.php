@extends('user.layouts.master')

@section('title', 'Booking Status')

@section('user-content')
<div class="container py-5">
    <div class="card shadow border-0">
        <div class="card-body text-center">

            @php
                $payment = $booking->payment ?? null; // booking hasOne payment
                $isPaid  = ($booking->payment_status ?? 'unpaid') === 'paid';
            @endphp

            {{-- Top Message --}}
            @if($isPaid)
                <h2 class="text-success mb-3">Payment Successful!</h2>
                <p class="mb-4">Your booking is confirmed and payment has been received.</p>
            @else
                <h2 class="text-warning mb-3">Booking Created (Payment Pending)</h2>
                <p class="mb-4">Your booking is placed. Please complete payment to confirm online payments.</p>
            @endif

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success text-start">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger text-start">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <hr>

            <div class="row text-start mt-4">
                <div class="col-md-6">
                    <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>

                    <p><strong>Vehicle:</strong>
                        {{ $booking->vehicle->name
                            ?? (($booking->vehicle->brand ?? '').' '.($booking->vehicle->model ?? ''))
                            ?? 'N/A' }}
                    </p>

                    <p><strong>Service:</strong> {{ ucfirst($booking->service) }}</p>

                    <p><strong>Booking Status:</strong>
                        <span class="badge {{ $booking->status === 'confirmed' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </p>

                    <p><strong>Payment Status:</strong>
                        <span class="badge {{ $isPaid ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($booking->payment_status ?? 'unpaid') }}
                        </span>
                    </p>

                    @if($payment)
                        <p><strong>Payment Method:</strong> {{ strtoupper($payment->method) }}</p>
                        <p><strong>Payment Record Status:</strong> {{ ucfirst($payment->status) }}</p>
                    @endif
                </div>

                <div class="col-md-6">
                    <p><strong>Pickup:</strong> {{ $booking->pickup_location }}</p>
                    <p><strong>Drop:</strong> {{ $booking->drop_location }}</p>

                    <p><strong>Pickup Date:</strong>
                        {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y h:i A') }}
                    </p>

                    <p><strong>Drop Date:</strong>
                        {{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y h:i A') }}
                    </p>
                </div>
            </div>

            <hr>

            <h4 class="mt-3">
                Total Price: Rs. {{ number_format($booking->total_price, 2) }}
            </h4>

            {{-- Action Buttons --}}
            <div class="mt-4 d-flex justify-content-center gap-2 flex-wrap">

                <a href="{{ route('home') }}" class="btn btn-primary">
                    Back to Home
                </a>

                <a href="{{ route('user.booking.index') }}" class="btn btn-outline-secondary">
                    My Bookings
                </a>

                @if($payment && in_array($payment->status, ['completed', 'refunded']))
                    <a href="{{ route('user.booking.invoice', $booking->id) }}" class="btn btn-outline-dark">
                        Download Invoice
                    </a>
                @endif

                @if(!$isPaid)
                    <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-success">
                        Pay Now
                    </a>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
