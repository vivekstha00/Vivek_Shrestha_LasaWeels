@extends('user.layouts.master')

@section('title', 'Booking Status')

@section('user-content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            @php
                $payment = $booking->payment ?? null; // booking hasOne payment
                $isPaid  = ($booking->payment_status ?? 'unpaid') === 'paid';
                $canDownloadInvoice = $payment && (
                    in_array($payment->status, ['completed', 'refunded'], true)
                    || $isPaid
                );
            @endphp

            {{-- Success Header --}}
            <div class="text-center mb-4">
                @if($isPaid)
                    <div class="mb-3">
                        <i class="fa-solid fa-circle-check fa-4x text-success"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2">Payment Successful!</h2>
                    <p class="text-muted">Your booking is confirmed and payment has been received.</p>
                @else
                    <div class="mb-3">
                        <i class="fa-solid fa-clock fa-4x text-warning"></i>
                    </div>
                    <h2 class="fw-bold text-warning mb-2">Booking Created</h2>
                    <p class="text-muted">Your booking is placed. Please complete payment to confirm.</p>
                @endif
            </div>

            {{-- Booking Details Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    {{-- Flash messages --}}
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

                    {{-- Booking Info --}}
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border-start border-primary border-4 ps-3">
                                <h5 class="fw-bold mb-3 text-primary">Booking Details</h5>
                                <div class="mb-2">
                                    <strong>Booking ID:</strong> #{{ $booking->id }}
                                </div>
                                <div class="mb-2">
                                    <strong>Vehicle:</strong>
                                    {{ $booking->vehicle->name
                                        ?? (($booking->vehicle->brand ?? '').' '.($booking->vehicle->model ?? ''))
                                        ?? 'N/A' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Service:</strong> {{ ucfirst($booking->service) }}
                                </div>
                                <div class="mb-2">
                                    <strong>Status:</strong>
                                    <span class="badge {{ $booking->status === 'confirmed' ? 'bg-success' : 'bg-warning' }} ms-1">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <strong>Payment:</strong>
                                    <span class="badge {{ $isPaid ? 'bg-success' : 'bg-danger' }} ms-1">
                                        {{ ucfirst($booking->payment_status ?? 'unpaid') }}
                                    </span>
                                </div>
                                @if($payment)
                                    <div class="mb-2">
                                        <strong>Method:</strong> {{ strtoupper($payment->method) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border-start border-info border-4 ps-3">
                                <h5 class="fw-bold mb-3 text-info">Trip Details</h5>
                                <div class="mb-2">
                                    <strong>Pickup:</strong> {{ $booking->pickup_location }}
                                </div>
                                <div class="mb-2">
                                    <strong>Drop:</strong> {{ $booking->drop_location }}
                                </div>
                                <div class="mb-2">
                                    <strong>Pickup Date:</strong>
                                    {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y h:i A') }}
                                </div>
                                <div class="mb-2">
                                    <strong>Drop Date:</strong>
                                    {{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Price Summary --}}
                    <div class="text-center mt-4 p-3 bg-light rounded">
                        <h4 class="fw-bold text-primary mb-0">
                            Total Price: Rs. {{ number_format($booking->total_price, 2) }}
                        </h4>
                    </div>

                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="text-center mt-4">
                <div class="row g-2 justify-content-center">
                    <div class="col-auto">
                        <a href="{{ route('home') }}" class="btn btn-primary px-4">
                            <i class="fa-solid fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('user.booking.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fa-solid fa-list me-2"></i>My Bookings
                        </a>
                    </div>
                    @if($canDownloadInvoice)
                        <div class="col-auto">
                            <a href="{{ route('user.booking.invoice', $booking->id) }}" class="btn btn-outline-dark px-4">
                                <i class="fa-solid fa-download me-2"></i>Download Invoice
                            </a>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary px-4"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#sendInvoiceEmailForm"
                                    aria-expanded="{{ $errors->has('invoice_email') ? 'true' : 'false' }}"
                                    aria-controls="sendInvoiceEmailForm">
                                <i class="fa-solid fa-envelope me-2"></i>Send Invoice Email
                            </button>
                        </div>
                    @endif
                    @if(!$isPaid)
                        <div class="col-auto">
                            <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-success px-4">
                                <i class="fa-solid fa-credit-card me-2"></i>Pay Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($canDownloadInvoice)
                <div class="collapse {{ $errors->has('invoice_email') ? 'show' : '' }} mt-4" id="sendInvoiceEmailForm">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fa-solid fa-paper-plane me-2 text-primary"></i>Send Invoice by Email
                            </h5>
                            <p class="text-muted mb-3">
                                Enter any email address. You can send the invoice to yourself or to someone else.
                            </p>

                            <form method="POST" action="{{ route('user.booking.invoice.email', $booking->id) }}">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-8">
                                        <label for="invoice_email" class="form-label fw-semibold">Recipient Email</label>
                                        <input type="email"
                                               id="invoice_email"
                                               name="invoice_email"
                                               class="form-control @error('invoice_email') is-invalid @enderror"
                                               value="{{ old('invoice_email', auth()->user()->email ?? '') }}"
                                               placeholder="Enter email address"
                                               required>
                                        @error('invoice_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-envelope-circle-check me-2"></i>Send Invoice
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
