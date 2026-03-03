@extends('user.layouts.master')

@section('title', 'Booking Details')

@section('user-content')
<div class="container py-5">

    {{-- Flash + errors --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    @php
        $payment = $booking->payment ?? null;
        $isPaid = ($booking->payment_status ?? 'unpaid') === 'paid';

        $vehicleName = $booking->vehicle->name
            ?? (($booking->vehicle->brand ?? '').' '.($booking->vehicle->model ?? ''))
            ?? 'N/A';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Booking #{{ $booking->id }}</h3>
            <small class="text-muted">Created at: {{ optional($booking->created_at)->format('d M Y, h:i A') }}</small>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('user.booking.index') }}" class="btn btn-outline-secondary">
                ← My Bookings
            </a>

            @if(!$isPaid)
                <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-success">
                    Pay Now
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">

        {{-- LEFT: booking + trip --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="mb-3">Booking Information</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Vehicle:</strong> {{ $vehicleName }}</p>
                            <p class="mb-1"><strong>Service:</strong> {{ ucfirst($booking->service) }}</p>
                            <p class="mb-1">
                                <strong>Booking Status:</strong>
                                <span class="badge {{ $booking->status === 'confirmed' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                            <p class="mb-1">
                                <strong>Payment Status:</strong>
                                <span class="badge {{ $isPaid ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($booking->payment_status ?? 'unpaid') }}
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p class="mb-1"><strong>Pickup:</strong> {{ $booking->pickup_location }}</p>
                            <p class="mb-1"><strong>Drop:</strong> {{ $booking->drop_location }}</p>
                            <p class="mb-1">
                                <strong>Pickup Date:</strong>
                                {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y h:i A') }}
                            </p>
                            <p class="mb-1">
                                <strong>Drop Date:</strong>
                                {{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y h:i A') }}
                            </p>
                        </div>
                    </div>

                    @if(!empty($booking->special_request))
                        <hr>
                        <h6 class="mb-2">Special Request</h6>
                        <p class="mb-0 text-muted">{{ $booking->special_request }}</p>
                    @endif

                    @if($booking->service === 'driver')
                        <hr>
                        <h6 class="mb-2">Driver</h6>
                        @if($booking->driver)
                            <p class="mb-1"><strong>Name:</strong> {{ $booking->driver->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $booking->driver->phone ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>License:</strong> {{ $booking->driver->license_number ?? 'N/A' }}</p>
                        @else
                            <p class="mb-0 text-muted">Driver info not available.</p>
                        @endif
                    @endif

                </div>
            </div>
        </div>

        {{-- RIGHT: payment summary --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="mb-3">Payment Summary</h5>

                    <p class="mb-1"><strong>Total Price:</strong> Rs. {{ number_format($booking->total_price, 2) }}</p>

                    @if(!is_null($booking->security_deposit))
                        <p class="mb-1"><strong>Security Deposit:</strong> Rs. {{ number_format($booking->security_deposit, 2) }}</p>
                    @endif

                    <hr>

                    @if($payment)
                        <p class="mb-1"><strong>Method:</strong> {{ strtoupper($payment->method) }}</p>
                        <p class="mb-1">
                            <strong>Payment Record:</strong>
                            <span class="badge {{ $payment->status === 'completed' ? 'bg-success' : ($payment->status === 'failed' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </p>

                        @if(!empty($payment->gateway_reference))
                            <p class="mb-0 small text-muted">
                                Gateway Ref: {{ $payment->gateway_reference }}
                            </p>
                        @endif
                    @else
                        <p class="text-muted mb-0">No payment record yet.</p>
                    @endif

                    @if(!$isPaid)
                        <div class="mt-3">
                            <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-success w-100">
                                Proceed to Payment
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
