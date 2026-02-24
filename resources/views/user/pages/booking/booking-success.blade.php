@extends('user.layouts.master')

@section('title', 'Booking Successful')

@section('user-content')
<div class="container py-5">
    <div class="card shadow border-0">
        <div class="card-body text-center">

            <h2 class="text-success mb-3">
                🎉 Booking Successful!
            </h2>

            <p class="mb-4">
                Your booking has been placed successfully.
            </p>

            <hr>

            <div class="row text-start mt-4">
                <div class="col-md-6">
                    <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                    <p><strong>Vehicle:</strong> {{ $booking->vehicle->name ?? 'N/A' }}</p>
                    <p><strong>Service:</strong> {{ ucfirst($booking->service) }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge bg-warning">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </p>
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

            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    Back to Home
                </a>

                <a href="#" class="btn btn-outline-secondary">
                    My Bookings
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
