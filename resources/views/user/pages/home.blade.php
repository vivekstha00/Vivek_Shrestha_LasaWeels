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
        .offer-banner {
            min-height: 320px;
            background: #fff;
        }

        .offer-banner-img {
            height: 320px;
            object-fit: cover;
            display: block;
        }

        .offer-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                90deg,
                rgba(15, 23, 42, 0.86) 0%,
                rgba(15, 23, 42, 0.56) 38%,
                rgba(15, 23, 42, 0.16) 100%
            );
        }

        .offer-content {
            max-width: 560px;
            z-index: 2;
            padding-left: 10px;
        }

        .offer-badge {
            display: inline-block;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.22);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .offer-discount {
            font-size: 2rem;
            font-weight: 800;
            color: #86efac;
            line-height: 1.2;
        }

        .offer-control-icon {
            background-color: rgba(15, 23, 42, 0.55);
            border-radius: 50%;
            padding: 18px;
        }

        .offer-indicator-btn {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            border: 0;
            background: #cbd5e1;
            padding: 0;
        }

        .offer-indicator-btn.active {
            background: #16a34a;
        }

        @media (max-width: 768px) {
            .offer-banner,
            .offer-banner-img {
                height: 260px;
                min-height: 260px;
            }

            .offer-content {
                max-width: 100%;
                padding-right: 20px !important;
            }

            .offer-content h2 {
                font-size: 1.35rem;
            }

            .offer-discount {
                font-size: 1.4rem;
            }
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

<section class="py-5 bg-light border-top">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">How It Works</h2>
            <p class="text-muted">Simple steps to your perfect ride</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <img src="{{ asset('images/about/step-search.jpg') }}" alt="Search Vehicles" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                </div>
                <h5 class="fw-bold">1. Search & Choose</h5>
                <p class="text-muted">Browse our wide selection of vehicles by location, dates, and preferences.</p>
            </div>

            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <img src="{{ asset('images/about/step-book.jpg') }}" alt="Book Online" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                </div>
                <h5 class="fw-bold">2. Book Online</h5>
                <p class="text-muted">Complete your booking securely with our easy online payment system.</p>
            </div>

            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <img src="{{ asset('images/about/step-drive.jpg') }}" alt="Pick Up & Drive" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                </div>
                <h5 class="fw-bold">3. Pick Up & Drive</h5>
                <p class="text-muted">Collect your vehicle from the vendor and enjoy your journey.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose LasaWheels</h2>
            <p class="text-muted">Built for convenience, trust, and better rental experience.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <img src="{{ asset('images/about/icon-search.png') }}" alt="Easy Booking" style="width: 50px; height: 50px;">
                        </div>
                        <h5 class="fw-bold">Easy Booking</h5>
                        <p class="text-muted mb-0">Search, choose and book vehicles in a few simple steps.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <img src="{{ asset('images/about/icon-trust.png') }}" alt="Trusted Vendors" style="width: 50px; height: 50px;">
                        </div>
                        <h5 class="fw-bold">Trusted Vendors</h5>
                        <p class="text-muted mb-0">Vehicle listings are managed through verified vendor accounts.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <img src="{{ asset('images/about/icon-payment.png') }}" alt="Secure Payments" style="width: 50px; height: 50px;">
                        </div>
                        <h5 class="fw-bold">Secure Payments</h5>
                        <p class="text-muted mb-0">Digital payment flow helps users complete bookings safely.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <img src="{{ asset('images/about/icon-review.png') }}" alt="Reviews & Ratings" style="width: 50px; height: 50px;">
                        </div>
                        <h5 class="fw-bold">Reviews & Ratings</h5>
                        <p class="text-muted mb-0">Users can share their experience after completing the trip.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold text-dark mb-1">Latest Blogs</h3>
                <p class="text-muted mb-0">Tips, guides and updates for better rentals</p>
            </div>
            <a href="{{ route('blog.index') }}" class="btn btn-outline-success">View All</a>
        </div>

        <div class="row g-4">
            @forelse($latestBlogs as $post)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                        @if($post->cover_image)
                            <img src="{{ asset('storage/'.$post->cover_image) }}"
                                 class="card-img-top"
                                 alt="{{ $post->title }}"
                                 style="height: 220px; object-fit: cover;">
                        @endif

                        <div class="card-body d-flex flex-column">
                            <p class="text-muted small mb-2">{{ $post->published_at?->format('d M Y') }}</p>
                            <h5 class="card-title fw-bold">{{ $post->title }}</h5>
                            <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}</p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-outline-dark mt-auto">Read More</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border text-center mb-0">No blog posts published yet.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

@if($activeOffers->isNotEmpty())
    <section class="py-5 border-top">
        <div class="container">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark mb-2">Active Offers & Discounts</h3>
                <p class="text-muted">Limited time offers on vehicle rentals</p>
            </div>

            <div id="offerCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($activeOffers as $index => $offer)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="offer-banner rounded-4 overflow-hidden shadow-sm position-relative">
                                <img src="{{ asset('images/offers/discount-banner.jpg') }}"
                                    class="w-100 offer-banner-img"
                                    alt="Offer banner">

                                <div class="offer-overlay"></div>

                                <div class="offer-content position-absolute top-50 start-0 translate-middle-y text-white px-4 px-md-5">
                                    <div class="offer-badge mb-3">Limited Time Offer</div>

                                    <h2 class="fw-bold mb-2">
                                        {{ $offer->title }}
                                    </h2>

                                    <p class="mb-2 fs-5">
                                        Use Promo Code:
                                        <span class="fw-bold text-warning">{{ $offer->code }}</span>
                                    </p>

                                    <div class="offer-discount mb-2">
                                        @if($offer->type === 'percentage')
                                            {{ $offer->value }}% OFF
                                        @else
                                            NPR {{ number_format($offer->value) }} OFF
                                        @endif
                                    </div>

                                    @if($offer->max_discount_amount)
                                        <p class="mb-2 small">
                                            Max Discount: NPR {{ number_format($offer->max_discount_amount) }}
                                        </p>
                                    @endif

                                    @if($offer->valid_until)
                                        <p class="mb-3 small">
                                            Valid Until: {{ $offer->valid_until->format('d M Y') }}
                                        </p>
                                    @endif

                                    <a href="{{ route('vehicles.index') }}" class="btn btn-success px-4 rounded-pill fw-semibold">
                                        Rent Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($activeOffers->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#offerCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon offer-control-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>

                    <button class="carousel-control-next" type="button" data-bs-target="#offerCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon offer-control-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>

            @if($activeOffers->count() > 1)
                <div class="d-flex justify-content-center gap-2 mt-3">
                    @foreach($activeOffers as $index => $offer)
                        <button type="button"
                                class="offer-indicator-btn {{ $index === 0 ? 'active' : '' }}"
                                data-bs-target="#offerCarousel"
                                data-bs-slide-to="{{ $index }}"
                                aria-label="Slide {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold text-dark mb-2">Have any Question?</h3>
            <p class="text-muted">Here are common questions about vehicle rental on LasaWheels.</p>
        </div>

        <div class="accordion accordion-flush bg-white rounded-4 shadow-sm overflow-hidden" id="homeFaqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne">
                        What documents are required to rent a vehicle?
                    </button>
                </h2>
                <div id="faqCollapseOne" class="accordion-collapse collapse show" aria-labelledby="faqHeadingOne" data-bs-parent="#homeFaqAccordion">
                    <div class="accordion-body text-secondary">
                        You usually need a valid citizenship/passport, driving license, and sometimes a refundable security deposit based on vehicle type.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                        Can I book a self-drive vehicle for multiple days?
                    </button>
                </h2>
                <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#homeFaqAccordion">
                    <div class="accordion-body text-secondary">
                        Yes, you can select your pick-up and drop-off date/time while searching and book for as many days as available.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                        Is fuel included in the rental price?
                    </button>
                </h2>
                <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree" data-bs-parent="#homeFaqAccordion">
                    <div class="accordion-body text-secondary">
                        In most self-drive bookings, fuel is not included. Vehicles are provided with a set level and should be returned similarly.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour">
                        What happens if I return the vehicle late?
                    </button>
                </h2>
                <div id="faqCollapseFour" class="accordion-collapse collapse" aria-labelledby="faqHeadingFour" data-bs-parent="#homeFaqAccordion">
                    <div class="accordion-body text-secondary">
                        Late returns may include extra hourly/day charges depending on vendor policy. Always inform the vendor early if delays are expected.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFive" aria-expanded="false" aria-controls="faqCollapseFive">
                        Can someone else drive the rented vehicle?
                    </button>
                </h2>
                <div id="faqCollapseFive" class="accordion-collapse collapse" aria-labelledby="faqHeadingFive" data-bs-parent="#homeFaqAccordion">
                    <div class="accordion-body text-secondary">
                        Only approved drivers listed during booking should drive. Additional drivers may require license verification with the vendor.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingSix">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseSix" aria-expanded="false" aria-controls="faqCollapseSix">
                        Are helmets and basic accessories included for bikes?
                    </button>
                </h2>
                <div id="faqCollapseSix" class="accordion-collapse collapse" aria-labelledby="faqHeadingSix" data-bs-parent="#homeFaqAccordion">
                    <div class="accordion-body text-secondary">
                        Yes, most bike rentals include helmets. You can confirm extras like phone holder, rain cover, or luggage rack with the vendor before pickup.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingSeven">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseSeven" aria-expanded="false" aria-controls="faqCollapseSeven">
                        Can I cancel or reschedule my booking?
                    </button>
                </h2>
                <div id="faqCollapseSeven" class="accordion-collapse collapse" aria-labelledby="faqHeadingSeven" data-bs-parent="#homeFaqAccordion">
                    <div class="accordion-body text-secondary">
                        Yes, cancellation and reschedule options depend on vendor rules and timing. Check your booking details for applicable terms.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingEight">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseEight" aria-expanded="false" aria-controls="faqCollapseEight">
                        What should I check before taking delivery of the vehicle?
                    </button>
                </h2>
                <div id="faqCollapseEight" class="accordion-collapse collapse" aria-labelledby="faqHeadingEight" data-bs-parent="#homeFaqAccordion">
                    <div class="accordion-body text-secondary">
                        Verify fuel level, existing scratches, brakes, lights, tires, and documents, then capture photos/videos for record before starting your trip.
                    </div>
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
