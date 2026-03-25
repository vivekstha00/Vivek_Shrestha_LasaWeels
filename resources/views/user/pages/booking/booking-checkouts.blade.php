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

    $basePriceForSummary = (float) ($basePrice ?? ($estimatedTotal ?? 0));
    $durationDiscountPercentValue = (float) ($durationDiscountPercent ?? 0);
    $durationDiscountAmountValue = (float) ($durationDiscountAmount ?? 0);

    $priceAfterDurationDiscount = max(0, $basePriceForSummary - $durationDiscountAmountValue);
    $actualPrice = (float) $priceAfterDurationDiscount;
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
                        <p class="mb-1">
                            Price per day:
                            <strong>NPR {{ number_format($pricePerDay, 2) }}</strong>
                        </p>
                        @if($durationDiscountPercentValue > 0)
                            <p class="mb-1 text-success">
                                Long booking offer applied:
                                <strong>{{ rtrim(rtrim(number_format($durationDiscountPercentValue, 2), '0'), '.') }}% off</strong>
                            </p>
                        @endif

                        @if(!empty($securityDeposit) && $selectedService === 'self')
                            <p class="mb-0 text-muted">
                                Refundable Security Deposit:
                                <strong>NPR {{ number_format($securityDeposit, 2) }}</strong>
                            </p>
                        @endif
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
                                <input
                                    type="text"
                                    class="form-control"
                                    name="pickup_location"
                                    value="{{ old('pickup_location', $data['pickup_location'] ?? '') }}"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Drop Location</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="drop_location"
                                    value="{{ old('drop_location', $data['drop_location'] ?? '') }}"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pickup Date & Time</label>
                                <input
                                    type="datetime-local"
                                    class="form-control"
                                    name="pickup_datetime"
                                    value="{{ old('pickup_datetime', isset($data['pickup_datetime']) ? \Carbon\Carbon::parse($data['pickup_datetime'])->format('Y-m-d\TH:i') : '') }}"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Drop Date & Time</label>
                                <input
                                    type="datetime-local"
                                    class="form-control"
                                    name="drop_datetime"
                                    value="{{ old('drop_datetime', isset($data['drop_datetime']) ? \Carbon\Carbon::parse($data['drop_datetime'])->format('Y-m-d\TH:i') : '') }}"
                                    required
                                >
                            </div>
                        </div>

                        {{-- Driver Selection --}}
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

                        {{-- Discount Choice --}}
                        <div class="card mt-4 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Apply Discount</h5>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input discount-choice"
                                        type="radio"
                                        name="discount_choice"
                                        id="discount_none"
                                        value="none"
                                        {{ old('discount_choice', 'none') === 'none' ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="discount_none">
                                        No discount
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input discount-choice"
                                        type="radio"
                                        name="discount_choice"
                                        id="discount_loyalty"
                                        value="loyalty"
                                        {{ old('discount_choice') === 'loyalty' ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="discount_loyalty">
                                        Use Loyalty Points
                                    </label>
                                </div>

                                <div id="loyalty_box" class="border rounded p-3 mb-3" style="display: none;">
                                    <div class="mb-2">
                                        <div class="small text-muted mb-1">Available Points</div>
                                        <div class="fw-bold text-primary">{{ $availablePoints ?? 0 }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="small text-muted mb-1">Max Redeemable</div>
                                        <div class="fw-bold text-success">{{ $maxRedeemablePoints ?? 0 }}</div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">Redeem Points</label>
                                        <input
                                            type="number"
                                            id="redeem_points"
                                            name="redeem_points"
                                            class="form-control"
                                            min="0"
                                            max="{{ $maxRedeemablePoints ?? 0 }}"
                                            step="1"
                                            value="{{ old('redeem_points', 0) }}"
                                            placeholder="Enter points"
                                        >
                                        <small class="text-muted">1 point = NPR 1. Minimum 100 points.</small>
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input discount-choice"
                                        type="radio"
                                        name="discount_choice"
                                        id="discount_code_option"
                                        value="code"
                                        {{ old('discount_choice') === 'code' ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="discount_code_option">
                                        Use Discount Code
                                    </label>
                                </div>

                                <div id="code_box" class="border rounded p-3" style="display: none;">
                                    <label class="form-label fw-semibold">Discount Code</label>
                                    <input
                                        type="text"
                                        id="discount_code"
                                        name="discount_code"
                                        class="form-control"
                                        value="{{ old('discount_code') }}"
                                        placeholder="Enter code like NEWYEAR26"
                                    >

                                    @if(!empty($activeDiscountCodes) && $activeDiscountCodes->count())
                                        <div class="mt-3">
                                            <div class="small text-muted mb-2">Available offers</div>

                                            @foreach($activeDiscountCodes as $offer)
                                                <div class="border rounded p-2 small bg-light mb-2">
                                                    <strong>{{ $offer->title }}</strong><br>
                                                    Code: <strong>{{ $offer->code }}</strong><br>

                                                    @if($offer->type === 'percentage')
                                                        Discount:
                                                        {{ rtrim(rtrim(number_format($offer->value, 2), '0'), '.') }}% off
                                                    @else
                                                        Discount:
                                                        NPR {{ number_format($offer->value, 2) }} off
                                                    @endif

                                                    @if($offer->max_discount_amount)
                                                        <br>Max Discount: NPR {{ number_format($offer->max_discount_amount, 2) }}
                                                    @endif

                                                    @if($offer->valid_until)
                                                        <br>Valid Until: {{ $offer->valid_until->format('Y-m-d h:i A') }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Special Request --}}
                        <div class="mt-4">
                            <label class="form-label">Special Request</label>
                            <textarea
                                name="special_request"
                                class="form-control"
                                rows="3"
                                placeholder="Any note for booking (optional)"
                            >{{ old('special_request', $data['special_request'] ?? '') }}</textarea>
                        </div>

                        {{-- Terms --}}
                        <div class="form-check mt-4">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="accept_terms"
                                value="1"
                                id="accept_terms"
                                {{ old('accept_terms') ? 'checked' : '' }}
                                required
                            >
                            <label class="form-check-label" for="accept_terms">
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

                    <div class="d-flex justify-content-between mb-2">
                        <span>Estimated Days</span>
                        <strong>{{ $days ?? 1 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Price per day</span>
                        <strong>NPR {{ number_format($pricePerDay, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Base Price</span>
                        <strong>NPR {{ number_format($basePriceForSummary, 2) }}</strong>
                    </div>

                    @if($durationDiscountPercentValue > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Long Duration Discount ({{ rtrim(rtrim(number_format($durationDiscountPercentValue, 2), '0'), '.') }}%)</span>
                            <strong class="text-primary">
                                - NPR {{ number_format($durationDiscountAmountValue, 2) }}
                            </strong>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2">
                        <span>Price After Duration Discount</span>
                        <strong id="actual_price" data-value="{{ $actualPrice }}">
                            NPR {{ number_format($actualPrice, 2) }}
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Checkout Discount Type</span>
                        <strong id="discount_type_label">None</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Checkout Discount Amount</span>
                        <strong class="text-danger" id="discount_price">- NPR 0.00</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fs-5">
                        <span class="fw-bold">Final Price</span>
                        <span class="fw-bold text-success" id="final_price">
                            NPR {{ number_format($actualPrice, 2) }}
                        </span>
                    </div>

                    @if(!empty($securityDeposit) && $selectedService === 'self')
                        <small class="text-muted d-block mt-2">
                            Security deposit is separate and refundable based on your booking policy.
                        </small>
                    @endif

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
            const actualPriceEl = document.getElementById('actual_price');
            const finalPriceEl = document.getElementById('final_price');
            const discountPriceEl = document.getElementById('discount_price');
            const discountTypeLabelEl = document.getElementById('discount_type_label');

            const redeemInput = document.getElementById('redeem_points');
            const discountCodeInput = document.getElementById('discount_code');

            const loyaltyBox = document.getElementById('loyalty_box');
            const codeBox = document.getElementById('code_box');

            const radioNone = document.getElementById('discount_none');
            const radioLoyalty = document.getElementById('discount_loyalty');
            const radioCode = document.getElementById('discount_code_option');

            const actualPrice = parseFloat(actualPriceEl?.dataset.value || 0);
            const maxRedeemable = parseInt(redeemInput?.max || 0);

            function formatNpr(amount) {
                return 'NPR ' + Number(amount).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function getSelectedDiscountChoice() {
                const checked = document.querySelector('input[name="discount_choice"]:checked');
                return checked ? checked.value : 'none';
            }

            function toggleDiscountFields() {
                const selected = getSelectedDiscountChoice();

                if (loyaltyBox) {
                    loyaltyBox.style.display = selected === 'loyalty' ? 'block' : 'none';
                }

                if (codeBox) {
                    codeBox.style.display = selected === 'code' ? 'block' : 'none';
                }

                if (selected !== 'loyalty' && redeemInput) {
                    redeemInput.value = 0;
                }

                if (selected !== 'code' && discountCodeInput) {
                    discountCodeInput.value = '';
                }

                updateSummary();
            }

            function updateSummary() {
                const selected = getSelectedDiscountChoice();

                let discount = 0;
                let label = 'None';

                if (selected === 'loyalty' && redeemInput) {
                    let points = parseInt(redeemInput.value || 0);

                    if (isNaN(points) || points < 0) {
                        points = 0;
                    }

                    if (points > maxRedeemable) {
                        points = maxRedeemable;
                    }

                    redeemInput.value = points;
                    discount = points;
                    label = 'Loyalty Points';
                }

                if (selected === 'code') {
                    label = 'Discount Code';
                    discount = 0;
                }

                const finalPrice = Math.max(0, actualPrice - discount);

                if (discountTypeLabelEl) {
                    discountTypeLabelEl.textContent = label;
                }

                if (discountPriceEl) {
                    discountPriceEl.textContent = '- ' + formatNpr(discount);
                }

                if (finalPriceEl) {
                    finalPriceEl.textContent = formatNpr(finalPrice);
                }
            }

            document.querySelectorAll('.discount-choice').forEach(function (radio) {
                radio.addEventListener('change', toggleDiscountFields);
            });

            if (redeemInput) {
                redeemInput.addEventListener('input', updateSummary);
            }

            toggleDiscountFields();
        });
    </script>
@endpush
@endsection
