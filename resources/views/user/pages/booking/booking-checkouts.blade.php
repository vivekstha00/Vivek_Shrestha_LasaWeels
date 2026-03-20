@extends('user.layouts.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('user-content')

@php
    $selectedService = old('service', $data['service'] ?? $service ?? 'self');
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
                            <strong>NPR {{ number_format($pricePerDay, 2) }}</strong>
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

                    <form id="bookingCheckoutForm" method="POST" action="{{ route('user.booking.store', $vehicle->id) }}">
                        @csrf

                        <input type="hidden" name="service" value="{{ $selectedService }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pickup Location</label>
                                <input type="text" class="form-control" name="pickup_location" value="{{ old('pickup_location', $data['pickup_location'] ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Drop Location</label>
                                <input type="text" class="form-control" name="drop_location" value="{{ old('drop_location', $data['drop_location'] ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pickup Date & Time</label>
                                <input type="datetime-local" class="form-control" name="pickup_datetime" value="{{ old('pickup_datetime', isset($data['pickup_datetime']) ? \Carbon\Carbon::parse($data['pickup_datetime'])->format('Y-m-d\TH:i') : '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Drop Date & Time</label>
                                <input type="datetime-local" class="form-control" name="drop_datetime" value="{{ old('drop_datetime', isset($data['drop_datetime']) ? \Carbon\Carbon::parse($data['drop_datetime'])->format('Y-m-d\TH:i') : '') }}" required>
                            </div>
                        </div>

                        {{-- Driver Selection Section --}}
                        @if($selectedService === 'driver')
                            <div class="card mt-4 border-primary">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-2">Driver Selection</h5>

                                    @if(!empty($selectedDriver))
                                        <div class="alert alert-success d-flex align-items-center mb-3">
                                            <span class="me-2 fs-5">✓</span>
                                            <div>
                                                <strong>{{ $selectedDriver->name }}</strong>
                                                (Rating: {{ $selectedDriver->rating ?? 'N/A' }} / 5)
                                            </div>
                                        </div>
                                        <input type="hidden" name="driver_id" value="{{ $selectedDriver->id }}">
                                        <a href="{{ route('user.driver.index') }}?vehicle_id={{ $vehicle->id }}&pickup_datetime={{ urlencode($data['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($data['drop_datetime'] ?? '') }}&pickup_location={{ urlencode($data['pickup_location'] ?? '') }}&drop_location={{ urlencode($data['drop_location'] ?? '') }}&service={{ $selectedService }}"
                                           class="btn btn-outline-primary btn-sm">
                                            Change Driver
                                        </a>
                                    @else
                                        <p class="text-muted mb-2">You need to select a driver before proceeding.</p>
                                        <a href="{{ route('user.driver.index') }}?vehicle_id={{ $vehicle->id }}&pickup_datetime={{ urlencode($data['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($data['drop_datetime'] ?? '') }}&pickup_location={{ urlencode($data['pickup_location'] ?? '') }}&drop_location={{ urlencode($data['drop_location'] ?? '') }}&service={{ $selectedService }}"
                                           class="btn btn-primary">
                                            Choose a Driver
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Terms --}}
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="accept_terms" value="1" required>
                            <label class="form-check-label">I agree to Terms & Conditions</label>
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

                    <div class="d-flex justify-content-between mb-2">
                        <span>Estimated Days</span>
                        <strong>{{ $days ?? 1 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Price per day</span>
                        <strong>NPR {{ number_format($pricePerDay, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Actual Price</span>
                        <strong id="actual_price" data-value="{{ $estimatedTotal ?? 0 }}">
                            NPR {{ number_format($estimatedTotal ?? 0, 2) }}
                        </strong>
                    </div>

                    @if(($availablePoints ?? 0) > 0)
                        <hr>

                        <div class="mb-2">
                            <div class="small text-muted mb-1">Available Points</div>
                            <div class="fw-bold text-primary">{{ $availablePoints ?? 0 }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-muted mb-1">Max Redeemable</div>
                            <div class="fw-bold text-success">{{ $maxRedeemablePoints ?? 0 }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Redeem Points</label>
                            <input
                                type="number"
                                id="redeem_points"
                                name="redeem_points"
                                form="bookingCheckoutForm"
                                class="form-control"
                                min="0"
                                max="{{ $maxRedeemablePoints ?? 0 }}"
                                step="1"
                                value="{{ old('redeem_points', 0) }}"
                                placeholder="Enter points"
                            >
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Loyalty Discount</span>
                            <strong class="text-danger" id="discount_price">- NPR 0.00</strong>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between fs-5">
                        <span class="fw-bold">Final Price</span>
                        <span class="fw-bold text-success" id="final_price">
                            NPR {{ number_format($estimatedTotal ?? 0, 2) }}
                        </span>
                    </div>

                    <small class="text-muted d-block mt-2">
                        Final price will be validated securely on server.
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const redeemInput = document.getElementById('redeem_points');
            const actualPriceEl = document.getElementById('actual_price');
            const discountPriceEl = document.getElementById('discount_price');
            const finalPriceEl = document.getElementById('final_price');

            if (!redeemInput || !actualPriceEl || !discountPriceEl || !finalPriceEl) {
                return;
            }

            const actualPrice = parseFloat(actualPriceEl.dataset.value || 0);
            const maxRedeemable = parseInt(redeemInput.max || 0);

            function formatNpr(amount) {
                return 'NPR ' + Number(amount).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function updateSummary() {
                let points = parseInt(redeemInput.value || 0);

                if (isNaN(points) || points < 0) {
                    points = 0;
                }

                if (points > maxRedeemable) {
                    points = maxRedeemable;
                }

                redeemInput.value = points;

                const discount = points;
                const finalPrice = Math.max(0, actualPrice - discount);

                discountPriceEl.textContent = '- ' + formatNpr(discount);
                finalPriceEl.textContent = formatNpr(finalPrice);
            }

            redeemInput.addEventListener('input', updateSummary);
            updateSummary();
        });
    </script>
@endpush
@endsection
