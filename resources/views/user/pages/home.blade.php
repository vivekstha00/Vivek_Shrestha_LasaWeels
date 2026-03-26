@extends('user.layouts.master')

@section('title', 'LasaWheels - Rent Your Ride')

@section('user-content')

<section class="hero-home d-flex align-items-center">
    <div class="container hero-content">
        <div class="row justify-content-center">
            <div class="col-lg-11 text-white">

                <h1 class="display-3 fw-bold mb-2">
                    Rent a <span class="text-success">Vehicle</span> Easily
                </h1>

                <p class="lead mb-4">
                    Choose 4-wheel or 2-wheel rentals in just a few easy steps.
                </p>

                <div class="bg-white text-dark rounded shadow p-4 p-md-5"
                     x-data="{
                        wheelType: '4_wheeler',
                        service: 'self',
                        setWheelType(type) {
                            this.wheelType = type;
                            if (type === '2_wheeler') {
                                this.service = 'self';
                            }
                        }
                     }">
                    <form method="GET" action="{{ route('user.search.vehicles') }}">
                        <input type="hidden" name="service" :value="service">
                        <input type="hidden" name="wheel_type" :value="wheelType">

                        <div class="row g-4 align-items-start">

                            <div class="col-lg-4">
                                <div class="fw-bold mb-3 fs-5">Choose vehicle category</div>

                                <div class="d-flex gap-3 mb-4">
                                    <button type="button"
                                            class="service-square"
                                            :class="wheelType === '4_wheeler' ? 'active' : ''"
                                            @click="setWheelType('4_wheeler')">
                                        <i class="fa-solid fa-car mb-2"></i>
                                        <span>4 Wheeler</span>
                                    </button>

                                    <button type="button"
                                            class="service-square"
                                            :class="wheelType === '2_wheeler' ? 'active' : ''"
                                            @click="setWheelType('2_wheeler')">
                                        <i class="fa-solid fa-motorcycle mb-2"></i>
                                        <span>2 Wheeler</span>
                                    </button>
                                </div>

                                <template x-if="wheelType === '4_wheeler'">
                                    <div>
                                        <div class="fw-bold mb-3 fs-6">Choose a service</div>
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
                                </template>

                                <template x-if="wheelType === '2_wheeler'">
                                    <div class="alert alert-light border rounded-3 mb-0">
                                        <strong>2 Wheeler:</strong> Self-drive only
                                    </div>
                                </template>
                            </div>

                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'From' : 'Pick Up Location'"></label>
                                        <input type="text" name="pickup_location" class="form-control"
                                               :readonly="service==='self'"
                                               :value="service==='self' ? 'Pokhara Matepani' : ''"
                                               :placeholder="service==='self' ? 'Pokhara Matepani' : 'Please enter pickup location'"
                                               required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'To' : 'Drop Off Location'"></label>
                                        <input type="text" name="drop_location" class="form-control"
                                               :placeholder="service==='self' ? 'Please enter destination' : 'Please enter drop location'"
                                               required>
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
                                        <span x-text="wheelType === '2_wheeler' ? 'Find Bikes & Scooters' : (service === 'driver' ? 'Find Driver Vehicles' : 'Find Vehicle')"></span>
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
    <section class="py-4 bg-white border-top">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="fw-bold mb-0">Active Offers & Discounts</h4>
            </div>

            <div class="position-relative">
                <div class="d-flex overflow-auto gap-3 pb-2 offers-scroller">
                    @foreach($activeOffers as $offer)
                        <div class="card border-0 shadow-sm rounded-4 flex-shrink-0" style="min-width: 280px; max-width: 320px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">{{ $offer->title }}</h6>
                                    <span class="badge bg-dark px-3 py-1">{{ $offer->code }}</span>
                                </div>

                                <p class="fs-5 fw-bold text-success mb-1">
                                    @if($offer->type === 'percentage')
                                        {{ $offer->value }}% OFF
                                    @else
                                        NPR {{ number_format($offer->value) }} OFF
                                    @endif
                                </p>

                                <div class="small text-muted mt-2">
                                    @if($offer->max_discount_amount)
                                        Max NPR {{ number_format($offer->max_discount_amount) }} •
                                    @endif
                                    Valid until {{ $offer->valid_until->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
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
