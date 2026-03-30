@extends('user.layouts.master')

@section('title', 'LasaWheels - Rent Your Ride')

@push('styles')
<style>
    .autocomplete-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.10);
        max-height: 280px;
        overflow-y: auto;
    }

    .autocomplete-item {
        padding: 12px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
    }

    .autocomplete-item:last-child {
        border-bottom: none;
    }

    .autocomplete-item:hover {
        background: #f8fafc;
    }

    .autocomplete-main {
        font-weight: 600;
        color: #0f172a;
        font-size: 15px;
    }

    .autocomplete-secondary {
        color: #64748b;
        font-size: 13px;
        margin-top: 2px;
    }
</style>
@endpush

@section('user-content')

<section class="hero-home d-flex align-items-center">
    <div class="container hero-content">
        <div class="row justify-content-center">
            <div class="col-lg-11 text-white">

                <h1 class="display-3 fw-bold mb-2">
                    Rent a <span class="text-success">Vehicle</span> Easily
                </h1>

                <p class="lead mb-4">
                    Choose self-drive or with-driver service in just a few easy steps.
                </p>

                <div class="bg-white text-dark rounded shadow p-4 p-md-5"
                     x-data="{
                        service: 'self'
                     }">
                    <form method="GET" action="{{ route('user.search.vehicles') }}">
                        <input type="hidden" name="service" :value="service">

                        <div class="row g-4 align-items-start">

                            <div class="col-lg-4">
                                <div>
                                    <div class="fw-bold mb-3 fs-5">Choose a service</div>

                                    <div class="d-flex gap-3">
                                        <button type="button"
                                                class="service-square"
                                                :class="service === 'self' ? 'active' : ''"
                                                @click="service='self'">
                                            <i class="fa-solid fa-car-side mb-2"></i>
                                            <span>Self Drive</span>
                                        </button>

                                        <button type="button"
                                                class="service-square"
                                                :class="service === 'driver' ? 'active' : ''"
                                                @click="service='driver'">
                                            <i class="fa-solid fa-user-tie mb-2"></i>
                                            <span>With Driver</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'From' : 'Pick Up Location'"></label>
                                        <div class="position-relative">
                                            <input
                                                type="text"
                                                id="pickup_location"
                                                name="pickup_location"
                                                class="form-control"
                                                placeholder="Search pickup location"
                                                autocomplete="off"
                                                required
                                            >
                                            <div id="pickup_suggestions" class="autocomplete-dropdown d-none"></div>
                                            <input type="hidden" name="pickup_lat" id="pickup_lat">
                                            <input type="hidden" name="pickup_lng" id="pickup_lng">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'To' : 'Drop Off Location'"></label>
                                        <div class="position-relative">
                                            <input
                                                type="text"
                                                id="drop_location"
                                                name="drop_location"
                                                class="form-control"
                                                placeholder="Search drop location"
                                                autocomplete="off"
                                                required
                                            >
                                            <div id="drop_suggestions" class="autocomplete-dropdown d-none"></div>
                                            <input type="hidden" name="drop_lat" id="drop_lat">
                                            <input type="hidden" name="drop_lng" id="drop_lng">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'From Date' : 'Pick Up Date & Time'"></label>
                                        <input type="datetime-local" name="pickup_datetime" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'To Date' : 'Drop Date & Time'"></label>
                                        <input type="datetime-local" name="drop_datetime" class="form-control" required>
                                    </div>

                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
                                        <span x-text="service === 'driver' ? 'Find Driver Vehicles' : 'Find Vehicles'"></span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

@if($activeOffers->isNotEmpty())
    <section class="py-5 bg-light border-top">
        <div class="container">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark mb-2">Active Offers & Discounts</h3>
                <p class="text-muted">Limited time offers on vehicle rentals</p>
            </div>

            <div class="row g-4">
                @foreach($activeOffers as $offer)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        @if($offer->type === 'percentage')
                                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fa-solid fa-percent"></i>
                                            </div>
                                        @else
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fa-solid fa-rupee-sign"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fw-bold mb-1">{{ $offer->title }}</h5>
                                        <span class="badge bg-secondary">{{ $offer->code }}</span>
                                    </div>
                                </div>

                                <div class="text-center mb-3">
                                    <div class="fs-4 fw-bold text-success mb-0">
                                        @if($offer->type === 'percentage')
                                            {{ $offer->value }}% OFF
                                        @else
                                            NPR {{ number_format($offer->value) }} OFF
                                        @endif
                                    </div>
                                </div>

                                <div class="small text-muted">
                                    @if($offer->max_discount_amount)
                                        <div class="mb-1">Max discount: NPR {{ number_format($offer->max_discount_amount) }}</div>
                                    @endif
                                    <div>Valid until: {{ $offer->valid_until->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($activeOffers->count() > 6)
                <div class="text-center mt-4">
                    <button class="btn btn-outline-success">
                        View All Offers
                    </button>
                </div>
            @endif
        </div>
    </section>
@endif

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <div class="col-md-4">
                <div class="bg-white rounded shadow-sm p-4 h-100">
                    <i class="fa-solid fa-car fa-2x text-success mb-3"></i>
                    <h5 class="fw-bold">4-Wheel Rentals</h5>
                    <p class="mb-0 text-secondary">Cars, SUVs, pickups and more.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="bg-white rounded shadow-sm p-4 h-100">
                    <i class="fa-solid fa-motorcycle fa-2x text-success mb-3"></i>
                    <h5 class="fw-bold">2-Wheel Rentals</h5>
                    <p class="mb-0 text-secondary">Bikes and scooters for easy city travel.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="bg-white rounded shadow-sm p-4 h-100">
                    <i class="fa-solid fa-headset fa-2x text-success mb-3"></i>
                    <h5 class="fw-bold">24/7 Support</h5>
                    <p class="mb-0 text-secondary">We’re available anytime.</p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    function setupPhotonAutocomplete(inputId, dropdownId, latId, lngId) {
        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        const latInput = document.getElementById(latId);
        const lngInput = document.getElementById(lngId);

        if (!input || !dropdown || !latInput || !lngInput) {
            return;
        }

        let debounceTimer = null;

        input.addEventListener('input', function () {
            const query = this.value.trim();

            latInput.value = '';
            lngInput.value = '';

            clearTimeout(debounceTimer);

            if (query.length < 2) {
                hideDropdown(dropdown);
                return;
            }

            debounceTimer = setTimeout(async () => {
                try {
                    const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(query)}&limit=8&lang=en&lat=28.3949&lon=84.1240`;
                    const response = await fetch(url);
                    const data = await response.json();

                    const nepalOnly = (data.features || []).filter(feature => {
                        const props = feature.properties || {};
                        const country = (props.country || '').toLowerCase().trim();
                        return country === 'nepal';
                    });

                    renderPhotonSuggestions(nepalOnly, dropdown, input, latInput, lngInput);
                } catch (error) {
                    console.error('Photon autocomplete error:', error);
                    hideDropdown(dropdown);
                }
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && e.target !== input) {
                hideDropdown(dropdown);
            }
        });
    }

    function renderPhotonSuggestions(features, dropdown, input, latInput, lngInput) {
        if (!features.length) {
            dropdown.innerHTML = `<div class="autocomplete-item">No Nepal locations found</div>`;
            dropdown.classList.remove('d-none');
            return;
        }

        dropdown.innerHTML = '';

        features.forEach(feature => {
            const props = feature.properties || {};
            const coords = feature.geometry?.coordinates || [];

            const name = props.name || 'Unknown place';
            const city = props.city || props.state || props.county || '';
            const country = props.country || '';
            const fullText = [name, city, country].filter(Boolean).join(', ');

            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.innerHTML = `
                <div class="autocomplete-main">${escapeHtml(name)}</div>
                <div class="autocomplete-secondary">${escapeHtml(fullText || name)}</div>
            `;

            item.addEventListener('click', () => {
                const lng = coords[0] || '';
                const lat = coords[1] || '';

                input.value = fullText || name;
                lngInput.value = lng;
                latInput.value = lat;

                hideDropdown(dropdown);
            });

            dropdown.appendChild(item);
        });

        dropdown.classList.remove('d-none');
    }

    function hideDropdown(dropdown) {
        dropdown.innerHTML = '';
        dropdown.classList.add('d-none');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupPhotonAutocomplete('pickup_location', 'pickup_suggestions', 'pickup_lat', 'pickup_lng');
        setupPhotonAutocomplete('drop_location', 'drop_suggestions', 'drop_lat', 'drop_lng');
    });
</script>
@endpush
