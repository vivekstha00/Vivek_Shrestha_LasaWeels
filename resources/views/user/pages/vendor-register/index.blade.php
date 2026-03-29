@extends('user.layouts.master')

@section('title', 'Become a Vendor')

@push('styles')
<style>
    .vendor-landing-page {
        padding: 0 0 70px;
        background: #f8fafc;
    }

    .vendor-hero {
        background: url('{{ asset('images/vendor-hero.png') }}') center/cover no-repeat;
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        border-radius: 0;
        min-height: 78vh;
        padding: 90px 50px;
        color: #fff;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
    }

    .vendor-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, rgba(15, 23, 42, 0.84) 0%, rgba(30, 41, 59, 0.72) 55%, rgba(15, 23, 42, 0.82) 100%);
        z-index: 1;
    }

    .vendor-hero-badge {
        display: inline-block;
        background: rgba(34, 197, 94, 0.15);
        color: #4ade80;
        border: 1px solid rgba(74, 222, 128, 0.35);
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .vendor-hero h1 {
        font-size: 48px;
        font-weight: 800;
        line-height: 1.15;
        margin-bottom: 18px;
    }

    .vendor-hero p {
        font-size: 17px;
        color: rgba(255,255,255,0.82);
        margin-bottom: 28px;
        max-width: 560px;
    }

    .vendor-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 25px;
    }

    .vendor-btn-primary {
        background: #22c55e;
        color: #fff;
        border: 0;
        border-radius: 14px;
        padding: 14px 28px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }

    .vendor-btn-primary:hover {
        background: #16a34a;
        color: #fff;
    }

    .vendor-btn-secondary {
        background: rgba(255,255,255,0.08);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.16);
        border-radius: 14px;
        padding: 14px 24px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .vendor-btn-secondary:hover {
        color: #fff;
        background: rgba(255,255,255,0.14);
    }

    .vendor-hero-content {
        position: relative;
        z-index: 2;
        max-width: 650px;
        margin: 0 auto;
    }

    .vendor-section {
        margin-top: 70px;
    }

    .vendor-section-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .vendor-section-title .eyebrow {
        color: #22c55e;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: 13px;
        margin-bottom: 10px;
        display: block;
    }

    .vendor-section-title h2 {
        font-size: 38px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .vendor-section-title p {
        color: #64748b;
        max-width: 700px;
        margin: 0 auto;
    }

    .vendor-feature-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        padding: 28px 24px;
        height: 100%;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.05);
    }

    .vendor-feature-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: #dcfce7;
        color: #16a34a;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        font-weight: 800;
    }

    .vendor-feature-card h4 {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .vendor-feature-card p {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.8;
    }

    .how-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 26px;
        padding: 34px;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .how-step-badge {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #22c55e;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 18px;
        margin-bottom: 16px;
    }

    .how-card h3 {
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 14px;
    }

    .how-card p {
        color: #64748b;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .how-image {
        width: 100%;
        max-height: 260px;
        object-fit: contain;
    }

    .vendor-cta {
        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
        border-radius: 28px;
        padding: 50px 35px;
        color: #fff;
        text-align: center;
        margin-top: 70px;
    }

    .vendor-cta h2 {
        font-weight: 800;
        font-size: 36px;
        margin-bottom: 12px;
    }

    .vendor-cta p {
        max-width: 720px;
        margin: 0 auto 24px;
        color: rgba(255,255,255,0.92);
    }

    @media (max-width: 991px) {
        .vendor-hero {
            min-height: 62vh;
            padding: 60px 24px;
            text-align: center;
        }

        .vendor-hero h1 {
            font-size: 36px;
        }

        .vendor-hero p {
            margin-left: auto;
            margin-right: auto;
        }

        .vendor-hero-actions {
            justify-content: center;
        }

        .how-card {
            text-align: center;
        }
    }

    @media (max-width: 576px) {
        .vendor-landing-page {
            padding: 24px 0 50px;
        }

        .vendor-hero h1 {
            font-size: 30px;
        }

        .vendor-section-title h2,
        .vendor-cta h2,
        .how-card h3 {
            font-size: 26px;
        }
    }
</style>
@endpush

@section('user-content')
<div class="vendor-landing-page">
    <div class="container">

        <div class="vendor-hero">
            <div class="row">
                <div class="col-lg-8">
                    <div class="vendor-hero-content">
                    <h1>Become Our Trusted Vehicle Rental Partner</h1>
                    <p>
                        Join LasaWheels and expand your business with a professional platform for bookings,
                        customer reach, document verification, and rental management.
                    </p>

                    <div class="vendor-hero-actions">
                        <a href="{{ $startRoute }}" class="vendor-btn-primary">{{ $ctaLabel }}</a>
                        <a href="#how-it-works" class="vendor-btn-secondary">How It Works</a>
                    </div>
                </div>
                    </div>
            </div>
        </div>

        <section class="vendor-section">
            <div class="vendor-section-title">
                <span class="eyebrow">Why Join Us</span>
                <h2>Why Partner With LasaWheels?</h2>
                <p>
                    Reach more customers, manage vehicles more efficiently, and grow your rental business with a trusted digital platform.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="vendor-feature-card">
                        <div class="vendor-feature-icon">1</div>
                        <h4>More Customer Reach</h4>
                        <p>Your vehicles get visibility on a modern rental platform where users can search, compare, and book easily.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="vendor-feature-card">
                        <div class="vendor-feature-icon">2</div>
                        <h4>Easy Business Management</h4>
                        <p>Manage bookings, vehicles, pricing, and drivers from one organized vendor dashboard.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="vendor-feature-card">
                        <div class="vendor-feature-icon">3</div>
                        <h4>Trusted Verification</h4>
                        <p>A structured registration and document review process helps build trust between vendors and customers.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="vendor-section" id="how-it-works">
            <div class="vendor-section-title">
                <span class="eyebrow">How It Works</span>
                <h2>Join in Four Simple Steps</h2>
                <p>
                    The vendor onboarding process is simple, clear, and designed to help you start listing vehicles with confidence.
                </p>
            </div>

            <div class="how-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="how-step-badge">1</div>
                        <h3>Create Your Account</h3>
                        <p>
                            Start by creating your vendor account with your basic details. This is the first step to begin the registration process on LasaWheels.
                        </p>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="{{ asset('images/vendor-step-account.png') }}" alt="Create account" class="how-image">
                    </div>
                </div>
            </div>

            <div class="how-card">
                <div class="row align-items-center g-4 flex-lg-row-reverse">
                    <div class="col-lg-6">
                        <div class="how-step-badge">2</div>
                        <h3>Complete Personal & Business Details</h3>
                        <p>
                            Enter your personal information, business details, and exact business location so your profile can be reviewed properly.
                        </p>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="{{ asset('images/vendor-step-business.png') }}" alt="Business details" class="how-image">
                    </div>
                </div>
            </div>

            <div class="how-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="how-step-badge">3</div>
                        <h3>Upload Verification Documents</h3>
                        <p>
                            Submit the required documents such as national ID, business license, and proof of address for verification and compliance.
                        </p>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="{{ asset('images/vendor-step-documents.png') }}" alt="Document upload" class="how-image">
                    </div>
                </div>
            </div>

            <div class="how-card">
                <div class="row align-items-center g-4 flex-lg-row-reverse">
                    <div class="col-lg-6">
                        <div class="how-step-badge">4</div>
                        <h3>Get Approved & Start Listing</h3>
                        <p>
                            Once your application is reviewed and approved by admin, you can access the vendor panel and begin adding vehicles to the platform.
                        </p>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="{{ asset('images/vendor-step-approval.png') }}" alt="Approval and listing" class="how-image">
                    </div>
                </div>
            </div>
        </section>

        <div class="vendor-cta">
            <h2>Ready to Join LasaWheels?</h2>
            <p>
                Register as a partner today and grow your rental business with a trusted, easy-to-manage platform.
            </p>
            <a href="{{ $startRoute }}" class="vendor-btn-primary bg-white text-success">{{ $ctaLabel }}</a>
        </div>

    </div>
</div>
@endsection
