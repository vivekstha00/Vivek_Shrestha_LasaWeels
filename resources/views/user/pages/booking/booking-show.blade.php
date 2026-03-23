@extends('user.layouts.master')

@section('title', 'Booking Details')



@section('user-content')
<div class="container py-5">

    {{-- Flash + errors --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $payment = $booking->payment ?? null;
        $isPaid = ($booking->payment_status ?? 'unpaid') === 'paid';

        $vehicleName = $booking->vehicle->name
            ?? trim(($booking->vehicle->brand ?? '') . ' ' . ($booking->vehicle->model ?? ''))
            ?: 'N/A';

        $statusBadge = match($booking->status) {
            'confirmed' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'active' => 'bg-primary',
            'completed' => 'bg-dark',
            'cancel_requested' => 'bg-warning text-dark',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };

        $canCancel = in_array($booking->status, ['pending', 'confirmed'], true)
            && now()->lt($booking->pickup_datetime->copy()->subDay())
            && (! $payment || ($payment->refund_status ?? 'none') === 'none');
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

            @if(!$isPaid && !in_array($booking->status, ['cancel_requested', 'cancelled'], true))
                <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-success">
                    Pay Now
                </a>
            @endif
        </div>
    </div>

    {{-- Cancellation / refund alerts --}}
    @if($booking->status === 'cancel_requested')
        <div class="alert alert-warning rounded-3">
            <strong>Cancellation Requested:</strong>
            Your cancellation request has been submitted and is pending admin review.
            @if($payment)
                <br><strong>Refund Amount:</strong> NPR {{ number_format($payment->refund_amount ?? 0, 2) }}
            @endif
        </div>
    @endif

    @if($booking->status === 'cancelled')
        <div class="alert alert-danger rounded-3">
            <strong>Booking Cancelled.</strong>
            @if($payment && $payment->refund_status === 'refunded')
                Refund has been processed successfully.
            @endif
        </div>
    @endif

    @if($payment && $payment->refund_status === 'rejected')
        <div class="alert alert-info rounded-3">
            <strong>Refund Request Rejected:</strong>
            {{ $payment->refund_note ?? 'Your refund request was not approved.' }}
        </div>
    @endif

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
                                <span class="badge {{ $statusBadge }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
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

                    @if(!empty($booking->cancellation_reason))
                        <hr>
                        <h6 class="mb-2">Cancellation Reason</h6>
                        <p class="mb-0 text-muted">{{ $booking->cancellation_reason }}</p>
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

            {{-- Cancel request card --}}
            @if($canCancel)
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">Request Cancellation</h5>
                        <p class="text-muted mb-3">
                            You can cancel this booking only if more than 24 hours remain before pickup.
                        </p>

                        <form action="{{ route('user.booking.cancel-request', $booking->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Cancellation Reason</label>
                                <textarea
                                    name="cancellation_reason"
                                    class="form-control"
                                    rows="3"
                                    required
                                >{{ old('cancellation_reason') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-outline-danger">
                                Request Cancellation
                            </button>
                        </form>
                    </div>
                </div>
            @elseif(in_array($booking->status, ['pending', 'confirmed'], true) && now()->gte($booking->pickup_datetime->copy()->subDay()))
                <div class="alert alert-secondary mt-4">
                    Booking cannot be cancelled within 24 hours of pickup.
                </div>
            @endif
        </div>

        {{-- RIGHT: payment summary --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="mb-3">Payment Summary</h5>

                    <p class="mb-1"><strong>Original Price:</strong> Rs. {{ number_format($booking->original_price ?? $booking->total_price, 2) }}</p>
                    <p class="mb-1"><strong>Discount:</strong> Rs. {{ number_format($booking->discount_amount ?? 0, 2) }}</p>
                    <p class="mb-1"><strong>Total Price:</strong> Rs. {{ number_format($booking->total_price, 2) }}</p>

                    @if(!is_null($booking->security_deposit))
                        <p class="mb-1"><strong>Security Deposit:</strong> Rs. {{ number_format($booking->security_deposit, 2) }}</p>
                    @endif

                    <hr>

                    @if($payment)
                        <p class="mb-1"><strong>Method:</strong> {{ strtoupper($payment->method) }}</p>
                        <p class="mb-1">
                            <strong>Payment Record:</strong>
                            <span class="badge {{ $payment->status === 'completed' ? 'bg-success' : ($payment->status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </p>

                        <p class="mb-1"><strong>Paid Amount:</strong> Rs. {{ number_format($payment->paid_amount ?? 0, 2) }}</p>

                        @if(($payment->refund_status ?? 'none') !== 'none')
                            <p class="mb-1">
                                <strong>Refund Status:</strong>
                                <span class="badge
                                    {{ $payment->refund_status === 'refunded' ? 'bg-success' : ($payment->refund_status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ ucfirst($payment->refund_status) }}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Refund Amount:</strong> Rs. {{ number_format($payment->refund_amount ?? 0, 2) }}</p>
                        @endif

                        @if(!empty($payment->refund_note))
                            <p class="mb-1"><strong>Refund Note:</strong> {{ $payment->refund_note }}</p>
                        @endif

                        @if(!empty($payment->gateway_reference))
                            <p class="mb-0 small text-muted">
                                Gateway Ref: {{ $payment->gateway_reference }}
                            </p>
                        @endif
                    @else
                        <p class="text-muted mb-0">No payment record yet.</p>
                    @endif

                    @if(!$isPaid && !in_array($booking->status, ['cancel_requested', 'cancelled'], true))
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
