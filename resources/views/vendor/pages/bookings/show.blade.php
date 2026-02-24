@extends('vendor.layouts.master')

@section('title', 'Booking Details')

@section('vendor-content')
<div class="container-fluid">

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

            <h5 class="mt-3">
                Total: Rs. {{ number_format($booking->total_price, 2) }}
            </h5>

            <a href="{{ route('vendor.bookings.index') }}"
               class="btn btn-secondary mt-3">
               Back
            </a>

        </div>
    </div>

</div>
@endsection
