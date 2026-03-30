@extends('user.layouts.master')

@section('title', 'About Us')

@section('user-content')
    {{-- Hero Section --}}
    <section class="py-5 text-white" style="background: linear-gradient(rgba(0,0,0,.6), rgba(0,0,0,.6)), url('{{ asset('images/about/about-hero.jpg') }}') center/cover no-repeat;">
        <div class="container text-center py-5">
            <h1 class="fw-bold display-4">About LasaWheels</h1>
            <p class="lead mb-4">
                Your trusted partner for smart and reliable vehicle rentals across Nepal.
            </p>
            <p class="mb-0">Cars, bikes, and scooters at your fingertips.</p>
        </div>
    </section>

    {{-- About Intro --}}
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <img src="{{ asset('images/about/about-team.jpg') }}" alt="LasaWheels Team" class="img-fluid rounded-4 shadow-sm">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3">Who We Are</h2>
                    <p class="text-muted">
                        LasaWheels is Nepal's leading vehicle rental platform, connecting travelers with trusted vendors for seamless booking experiences.
                    </p>
                    <p class="text-muted">
                        Founded with a vision to simplify vehicle rentals, we provide a user-friendly platform where you can browse, compare, and book vehicles with confidence.
                    </p>
                    <div class="mt-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="fw-bold text-primary">500+</h4>
                                    <small class="text-muted">Happy Customers</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="fw-bold text-primary">100+</h4>
                                    <small class="text-muted">Verified Vendors</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-5">
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

    {{-- Why Choose Us --}}
    <section class="py-5 bg-light">
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

    {{-- Featured Vehicles --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Featured Vehicles</h2>
                <p class="text-muted">Explore our popular rental options</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <img src="{{ asset('images/about/vehicle-car.jpg') }}" alt="Luxury Cars" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Luxury Cars</h5>
                            <p class="card-text text-muted">Premium sedans and SUVs for special occasions.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <img src="{{ asset('images/about/vehicle-bike.jpg') }}" alt="Motorcycles" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Motorcycles</h5>
                            <p class="card-text text-muted">Adventure bikes for exploring Nepal's terrains.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <img src="{{ asset('images/about/vehicle-scooter.jpg') }}" alt="Scooters" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Scooters</h5>
                            <p class="card-text text-muted">Perfect for city rides and short trips.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-5 bg-success text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Ready to Start Your Journey?</h2>
            <p class="mb-4">Join thousands of satisfied customers and book your vehicle today.</p>
            <a href="{{ route('home') }}" class="btn btn-light px-4 py-2 rounded-3 fw-semibold">Explore Vehicles</a>
        </div>
    </section>
@endsection
