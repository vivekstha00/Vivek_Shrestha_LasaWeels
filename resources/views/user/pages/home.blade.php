@extends('user.layouts.master')

@section('title', 'LasaWheels - Looking for a Car?')

@section('content')

{{-- HERO ONLY --}}
<section class="min-vh-100 d-flex align-items-center position-relative"
    style="background-image:url('{{ asset('images/hero.jpg') }}'); background-size:cover; background-position:center;">

    {{-- Dark overlay --}}
    <div class="position-absolute top-0 start-0 end-0 bottom-0 bg-dark opacity-50"></div>

    <div class="container position-relative py-5" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-11 text-white">

                <h1 class="display-3 fw-bold mb-2">
                    Looking for a <span class="text-success">Car</span>?
                </h1>
                <p class="lead mb-4">
                    Rent a car in just few easy steps.
                </p>

                {{-- Search/Booking Card --}}
                <div class="bg-white text-dark rounded shadow p-4 p-md-5"
                     x-data="{ service: 'self' }">
                    <form method="GET" action="#">
                        <input type="hidden" name="service" :value="service">

                        <div class="row g-4 align-items-start">
                            {{-- Left: service select --}}
                            <div class="col-lg-4">
                                <div class="fw-bold mb-3">Choose a service</div>

                                <div class="d-flex gap-3">
                                    <button type="button"
                                            class="btn w-100"
                                            :class="service==='self' ? 'btn-success' : 'btn-outline-success'"
                                            @click="service='self'">
                                        <i class="fa-solid fa-car me-2"></i>
                                        Self Drive
                                    </button>

                                    <button type="button"
                                            class="btn w-100"
                                            :class="service==='driver' ? 'btn-success' : 'btn-outline-success'"
                                            @click="service='driver'">
                                        <i class="fa-solid fa-user-tie me-2"></i>
                                        With Driver
                                    </button>
                                </div>
                            </div>

                            {{-- Right: inputs --}}
                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'From' : 'Pick Up Location'"></label>

                                        <input type="text"
                                               name="pickup_location"
                                               class="form-control"
                                               :readonly="service==='self'"
                                               :value="service==='self' ? 'Pokhara Matepani' : ''"
                                               :placeholder="service==='self' ? 'Pokhara Matepani' : 'Please enter pickup location'"
                                               required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'To' : 'Drop Off Location'"></label>

                                        <input type="text"
                                               name="drop_location"
                                               class="form-control"
                                               :placeholder="service==='self' ? 'Please enter to location' : 'Please enter drop location'"
                                               required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'From Date' : 'Pick Up Date & Time'"></label>

                                        <input type="datetime-local"
                                               name="pickup_datetime"
                                               class="form-control"
                                               required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                               x-text="service === 'self' ? 'To Date' : 'Drop Date & Time'"></label>

                                        <input type="datetime-local"
                                               name="drop_datetime"
                                               class="form-control"
                                               required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
                                        <span x-text="service === 'driver' ? 'Find Driver' : 'Find Vehicle'"></span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                {{-- END search card --}}

            </div>
        </div>
    </div>
</section>
{{-- ✅ HERO ENDS HERE --}}


{{-- FEATURES SECTION (separate, white background) --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <div class="col-md-4">
                <div class="bg-white rounded shadow-sm p-4 h-100">
                    <i class="fa-solid fa-car fa-2x text-success mb-3"></i>
                    <h5 class="fw-bold">Find the Perfect Ride</h5>
                    <p class="mb-0 text-secondary">Everyday cars to premium rides.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="bg-white rounded shadow-sm p-4 h-100">
                    <i class="fa-solid fa-dollar-sign fa-2x text-success mb-3"></i>
                    <h5 class="fw-bold">Transparent Pricing</h5>
                    <p class="mb-0 text-secondary">No hidden fees. Clear pricing.</p>
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
