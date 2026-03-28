@extends('user.layouts.master')

@section('title', 'Booking Checkout')

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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Header --}}
            <div class="text-center mb-4">
                <h2 class="fw-bold">Complete Your Booking</h2>
                <p class="text-muted">Review details and confirm your reservation</p>
            </div>

            {{-- Vehicle Summary --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @php
                                $img = $vehicle->primaryImage ?? $vehicle->images->first();
                            @endphp
                            @if($img)
                                <img src="{{ asset('storage/' . ltrim($img->path, '/')) }}"
                                     class="img-fluid rounded" style="max-height: 80px; object-fit: cover;" alt="Vehicle Image">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                    <i class="fa-solid fa-car text-secondary fs-2"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h4 class="fw-bold mb-2">{{ $vehicle->brand }} {{ $vehicle->model }}</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Service:</strong> {{ $selectedService === 'driver' ? 'With Driver' : 'Self Drive' }}</p>
                                    <p class="mb-1"><strong>Price per day:</strong> NPR {{ number_format($pricePerDay, 2) }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Duration:</strong> {{ $days ?? 1 }} days</p>
                                    @if($durationDiscountPercentValue > 0)
                                        <p class="mb-1 text-success"><strong>Discount:</strong> {{ rtrim(rtrim(number_format($durationDiscountPercentValue, 2), '0'), '.') }}% off</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="fs-3 fw-bold text-success">NPR {{ number_format($actualPrice, 2) }}</div>
                            <small class="text-muted">Total Amount</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Price Details Summary --}}
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="fw-bold mb-0">Price Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Base Price ({{ $days ?? 1 }} days × NPR {{ number_format($pricePerDay, 2) }})</span>
                        <strong>NPR {{ number_format($basePriceForSummary, 2) }}</strong>
                    </div>

                    @if($durationDiscountPercentValue > 0)
                        <div class="mb-2 d-flex justify-content-between text-success">
                            <span>Duration Discount ({{ rtrim(rtrim(number_format($durationDiscountPercentValue, 2), '0'), '.') }}%)</span>
                            <strong>- NPR {{ number_format($durationDiscountAmountValue, 2) }}</strong>
                        </div>
                    @endif

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Price after Duration Discount</span>
                        <strong>NPR {{ number_format($priceAfterDurationDiscount, 2) }}</strong>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span id="discount_type_label">Additional Discount</span>
                        <strong id="discount_price">- NPR 0.00</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fs-5">
                        <span class="fw-bold">Final Price</span>
                        <span class="fw-bold text-success" id="final_price">NPR {{ number_format($actualPrice, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Booking Form --}}
            <div class="card">
                <div class="card-body">

                    {{-- Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.booking.store', $vehicle->id) }}">
                        @csrf

                        <input type="hidden" name="service" value="{{ $selectedService }}">

                        {{-- Booking Details --}}
                        <h5 class="fw-bold mb-3">Booking Details</h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pickup Location</label>
                                <input type="text" class="form-control" name="pickup_location"
                                       value="{{ old('pickup_location', $data['pickup_location'] ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Drop Location</label>
                                <input type="text" class="form-control" name="drop_location"
                                       value="{{ old('drop_location', $data['drop_location'] ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pickup Date & Time</label>
                                <input type="datetime-local" class="form-control" name="pickup_datetime"
                                       value="{{ old('pickup_datetime', isset($data['pickup_datetime']) ? \Carbon\Carbon::parse($data['pickup_datetime'])->format('Y-m-d\TH:i') : '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Drop Date & Time</label>
                                <input type="datetime-local" class="form-control" name="drop_datetime"
                                       value="{{ old('drop_datetime', isset($data['drop_datetime']) ? \Carbon\Carbon::parse($data['drop_datetime'])->format('Y-m-d\TH:i') : '') }}" required>
                            </div>
                        </div>

                        {{-- Driver Selection --}}
                        @if($selectedService === 'driver')
                            <div class="mb-4">
                                <h5 class="fw-bold mb-3">Driver Selection</h5>

                                @if(!empty($selectedDriver))
                                    <div class="alert alert-success">
                                        <strong>{{ $selectedDriver->name }}</strong>
                                        (Rating: {{ $selectedDriver->rating ?? 'N/A' }} / 5)
                                    </div>
                                    <input type="hidden" name="driver_id" value="{{ $selectedDriver->id }}">

                                    <a href="{{ route('user.driver.index') }}?vehicle_id={{ $vehicle->id }}&pickup_datetime={{ urlencode($data['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($data['drop_datetime'] ?? '') }}&pickup_location={{ urlencode($data['pickup_location'] ?? '') }}&drop_location={{ urlencode($data['drop_location'] ?? '') }}&service={{ $selectedService }}"
                                       class="btn btn-outline-primary btn-sm">
                                        Change Driver
                                    </a>
                                @else
                                    <div class="alert alert-warning">
                                        You need to select a driver before proceeding.
                                        <a href="{{ route('user.driver.index') }}?vehicle_id={{ $vehicle->id }}&pickup_datetime={{ urlencode($data['pickup_datetime'] ?? '') }}&drop_datetime={{ urlencode($data['drop_datetime'] ?? '') }}&pickup_location={{ urlencode($data['pickup_location'] ?? '') }}&drop_location={{ urlencode($data['drop_location'] ?? '') }}&service={{ $selectedService }}"
                                           class="btn btn-primary btn-sm ms-2">
                                            Choose Driver
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Discounts --}}
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">Discount Options</h5>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input discount-choice" type="radio" name="discount_choice"
                                               id="discount_none" value="none" {{ old('discount_choice', 'none') === 'none' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_none">
                                            No discount
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input discount-choice" type="radio" name="discount_choice"
                                               id="discount_loyalty" value="loyalty" {{ old('discount_choice') === 'loyalty' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_loyalty">
                                            Use Loyalty Points
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input discount-choice" type="radio" name="discount_choice"
                                               id="discount_code_option" value="code" {{ old('discount_choice') === 'code' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_code_option">
                                            Use Discount Code
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Loyalty Points --}}
                            <div id="loyalty_box" class="mt-3 p-3 bg-light rounded" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div><strong>Available Points:</strong> {{ $availablePoints ?? 0 }}</div>
                                        <div><strong>Max Redeemable:</strong> {{ $maxRedeemablePoints ?? 0 }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Redeem Points</label>
                                        <input type="number" id="redeem_points" name="redeem_points" class="form-control"
                                               min="0" max="{{ $maxRedeemablePoints ?? 0 }}" step="1"
                                               value="{{ old('redeem_points', 0) }}">
                                        <small class="text-muted">1 point = NPR 1. Minimum 100 points.</small>
                                    </div>
                                </div>
                                <div class="mt-3 text-end">
                                    <button type="button" id="apply_loyalty" class="btn btn-primary btn-sm">
                                        Apply Loyalty Points
                                    </button>
                                </div>
                            </div>

                            {{-- Discount Code --}}
                            <div id="code_box" class="mt-3 p-3 bg-light rounded" style="display: none;">
                                <label class="form-label">Discount Code</label>
                                <input type="text" id="discount_code" name="discount_code" class="form-control"
                                       value="{{ old('discount_code') }}" placeholder="Enter code like NEWYEAR26">

                                @if(!empty($activeDiscountCodes) && $activeDiscountCodes->count())
                                    <div class="mt-3">
                                        <small class="text-muted">Available offers:</small>
                                        @foreach($activeDiscountCodes as $offer)
                                            <div class="border rounded p-2 mt-2 small">
                                                <strong>{{ $offer->title }}</strong> - Code: <strong>{{ $offer->code }}</strong>
                                                @if($offer->type === 'percentage')
                                                    ({{ rtrim(rtrim(number_format($offer->value, 2), '0'), '.') }}% off)
                                                @else
                                                    (NPR {{ number_format($offer->value, 2) }} off)
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-3 text-end">
                                    <button type="button" id="apply_code" class="btn btn-primary btn-sm">
                                        Apply Discount Code
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Special Request --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Special Request (Optional)</label>
                            <textarea name="special_request" class="form-control" rows="3"
                                      placeholder="Any special requirements or notes">{{ old('special_request', $data['special_request'] ?? '') }}</textarea>
                        </div>

                        {{-- Terms --}}
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="accept_terms" value="1"
                                       id="accept_terms" {{ old('accept_terms') ? 'checked' : '' }} required>
                                <label class="form-check-label" for="accept_terms">
                                    I agree to the Terms & Conditions
                                </label>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                Continue to Payment
                            </button>
                        </div>
                    </form>

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

            const applyLoyaltyBtn = document.getElementById('apply_loyalty');
            const applyCodeBtn = document.getElementById('apply_code');

            const actualPrice = {{ $actualPrice }};
            const maxRedeemable = {{ $maxRedeemablePoints ?? 0 }};

            let appliedDiscount = 0;
            let appliedDiscountType = 'None';

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

                // Reset applied discount when switching options
                if (selected === 'none') {
                    appliedDiscount = 0;
                    appliedDiscountType = 'None';
                    updateSummary();
                }
            }

            function updateSummary() {
                const finalPrice = Math.max(0, actualPrice - appliedDiscount);

                if (discountTypeLabelEl) {
                    discountTypeLabelEl.textContent = appliedDiscountType;
                }

                if (discountPriceEl) {
                    discountPriceEl.textContent = appliedDiscount > 0 ? '- ' + formatNpr(appliedDiscount) : '- NPR 0.00';
                }

                if (finalPriceEl) {
                    finalPriceEl.textContent = formatNpr(finalPrice);
                }
            }

            function applyLoyaltyPoints() {
                if (!redeemInput) return;

                let points = parseInt(redeemInput.value || 0);

                if (isNaN(points) || points < 0) {
                    alert('Please enter a valid number of points.');
                    return;
                }

                if (points > maxRedeemable) {
                    alert(`You can redeem maximum ${maxRedeemable} points.`);
                    redeemInput.value = maxRedeemable;
                    points = maxRedeemable;
                }

                if (points < 100 && points > 0) {
                    alert('Minimum 100 points required.');
                    return;
                }

                appliedDiscount = points;
                appliedDiscountType = 'Loyalty Points';
                updateSummary();

                // Disable the apply button and show success
                applyLoyaltyBtn.disabled = true;
                applyLoyaltyBtn.textContent = 'Applied';
                applyLoyaltyBtn.classList.remove('btn-primary');
                applyLoyaltyBtn.classList.add('btn-success');
            }

            function applyDiscountCode() {
                if (!discountCodeInput) return;

                const code = discountCodeInput.value.trim().toUpperCase();

                if (!code) {
                    alert('Please enter a discount code.');
                    return;
                }

                // For now, show a placeholder message since we can't validate codes client-side
                // In a real implementation, this would make an AJAX call to validate the code
                alert('Discount code validation will be performed when you submit the form. For demo purposes, assuming 10% discount applied.');

                // Demo: Apply a sample discount (10% of base price)
                appliedDiscount = Math.round(actualPrice * 0.1);
                appliedDiscountType = `Discount Code (${code})`;
                updateSummary();

                // Disable the apply button and show success
                applyCodeBtn.disabled = true;
                applyCodeBtn.textContent = 'Applied';
                applyCodeBtn.classList.remove('btn-primary');
                applyCodeBtn.classList.add('btn-success');
            }

            // Event listeners
            document.querySelectorAll('.discount-choice').forEach(function (radio) {
                radio.addEventListener('change', toggleDiscountFields);
            });

            if (applyLoyaltyBtn) {
                applyLoyaltyBtn.addEventListener('click', applyLoyaltyPoints);
            }

            if (applyCodeBtn) {
                applyCodeBtn.addEventListener('click', applyDiscountCode);
            }

            // Initialize
            toggleDiscountFields();
        });
    </script>
@endpush
@endsection
