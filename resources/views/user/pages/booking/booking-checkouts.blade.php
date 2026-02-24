@extends('user.layouts.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('user-content')

@php
    $selectedService = old('service', $service ?? 'self');

    $pricePerDay = $selectedService === 'driver'
        ? (float) ($vehicle->with_driver_price_per_day ?? $vehicle->price_per_day ?? 0)
        : (float) ($vehicle->price_per_day ?? 0);
@endphp

<div class="container mt-5 pt-5">
    <div class="row g-4">

        {{-- LEFT --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h3 class="fw-bold mb-4">Booking Checkout</h3>

                    {{-- Vehicle Info --}}
                    <div class="border rounded p-3 mb-4">
                        <h5 class="fw-bold">
                            {{ $vehicle->brand }} {{ $vehicle->model }}
                        </h5>
                        <p class="mb-1">
                            Service:
                            <strong>
                                {{ $selectedService === 'driver' ? 'With Driver' : 'Self Drive' }}
                            </strong>
                        </p>
                        <p class="mb-0">
                            Price per day:
                            <strong>NPR {{ number_format($pricePerDay,2) }}</strong>
                        </p>
                    </div>

                    {{-- Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.booking.store', $vehicle->id) }}">
                        @csrf

                        <input type="hidden" name="service" value="{{ $selectedService }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pickup Location</label>
                                <input type="text" class="form-control" name="pickup_location"
                                       value="{{ old('pickup_location') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Drop Location</label>
                                <input type="text" class="form-control" name="drop_location"
                                       value="{{ old('drop_location') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pickup Date & Time</label>
                                <input type="datetime-local"
                                       class="form-control"
                                       name="pickup_datetime"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Drop Date & Time</label>
                                <input type="datetime-local"
                                       class="form-control"
                                       name="drop_datetime"
                                       required>
                            </div>
                        </div>

                        {{-- Terms --}}
                        <div class="form-check mt-4">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="accept_terms"
                                   value="1"
                                   required>
                            <label class="form-check-label">
                                I agree to Terms & Conditions
                            </label>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success px-4">
                                Continue to Payment
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Price Summary</h5>

                    <div class="d-flex justify-content-between">
                        <span>Estimated Days</span>
                        <strong>{{ $days ?? 1 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Price per day</span>
                        <strong>NPR {{ number_format($pricePerDay,2) }}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fs-5">
                        <span class="fw-bold">Estimated Total</span>
                        <span class="fw-bold">
                            NPR {{ number_format($estimatedTotal ?? 0,2) }}
                        </span>
                    </div>

                    <small class="text-muted">
                        Final price will be calculated securely on server.
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
