@extends('user.layouts.master')

@section('user-content')

<!-- HERO SECTION -->
<section class="py-5" style="background: linear-gradient(rgba(15,23,42,0.85), rgba(15,23,42,0.85)), url('{{ asset('images/hero.jpg') }}') center/cover no-repeat;">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7 text-white">
                <h1 class="display-5 fw-bold mb-3">
                    Looking for a <span style="color:#F59E0B;">Vehicle</span>?
                </h1>
                <p class="lead mb-4">
                    Rent a vehicle in a few simple steps with LasaWheels.
                </p>

                <div class="d-flex gap-2">
                    <a href="{{ route('register') }}" class="btn btn-main btn-lg">
                        Get Started
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                        Login
                    </a>
                </div>

                <div class="mt-4 small" style="color: rgba(255,255,255,0.85);">
                    New users will be <strong>pending</strong> until admin approval.
                </div>
            </div>

            <div class="col-lg-5">
                <!-- SEARCH / CTA CARD (Simple for interim) -->
                <div class="card card-soft">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Quick Search (Demo)</h5>
                        <p class="text-muted mb-4">
                            Search will be fully implemented in the next milestone.
                        </p>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Pick Up Location</label>
                                <input type="text" class="form-control" placeholder="e.g., Kathmandu">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Drop Off Location</label>
                                <input type="text" class="form-control" placeholder="e.g., Lalitpur">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pick Up Date</label>
                                <input type="datetime-local" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Drop Date</label>
                                <input type="datetime-local" class="form-control">
                            </div>
                        </div>

                        <button type="button" class="btn btn-main w-100 mt-4">
                            Find Vehicle
                        </button>

                        <div class="text-muted small mt-3">
                            (This is UI only for interim.)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-5" style="background:#0F172A;">
    <div class="container py-4 text-white">
        <div class="row mb-4">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-2">How it works</h2>
                <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                    A simple process designed for a smooth rental experience.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="p-4 rounded-4 h-100" style="background: rgba(255,255,255,0.06);">
                    <div class="fw-bold" style="color:#F59E0B; font-size: 28px;">1</div>
                    <h5 class="mt-2">Create Account</h5>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                        Register and submit basic details.
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 rounded-4 h-100" style="background: rgba(255,255,255,0.06);">
                    <div class="fw-bold" style="color:#F59E0B; font-size: 28px;">2</div>
                    <h5 class="mt-2">Get Approved</h5>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                        Admin verifies and approves your account.
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 rounded-4 h-100" style="background: rgba(255,255,255,0.06);">
                    <div class="fw-bold" style="color:#F59E0B; font-size: 28px;">3</div>
                    <h5 class="mt-2">Choose Vehicle</h5>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                        Browse and select a suitable vehicle.
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-4 rounded-4 h-100" style="background: rgba(255,255,255,0.06);">
                    <div class="fw-bold" style="color:#F59E0B; font-size: 28px;">4</div>
                    <h5 class="mt-2">Book & Ride</h5>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                        Confirm booking and enjoy the trip.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURE STRIP -->
<section class="py-5" style="background:#F5F7FA;">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card card-soft h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-shield-alt mb-3" style="font-size:28px; color:#334155;"></i>
                        <h5 class="fw-bold">Verified Users</h5>
                        <p class="text-muted mb-0">Admin verification helps improve platform trust.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-soft h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-star mb-3" style="font-size:28px; color:#334155;"></i>
                        <h5 class="fw-bold">Premium Experience</h5>
                        <p class="text-muted mb-0">Clean UI with consistent branding and layout.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-soft h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-headset mb-3" style="font-size:28px; color:#334155;"></i>
                        <h5 class="fw-bold">Support Ready</h5>
                        <p class="text-muted mb-0">Support module will be extended in next milestone.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
