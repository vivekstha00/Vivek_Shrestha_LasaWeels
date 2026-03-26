@extends('user.layouts.master')

@push('styles')
<style>
    .vendor-register-page {
        padding-top: 30px;
        padding-bottom: 60px;
    }

    .register-wrapper {
        max-width: 980px;
        margin: 0 auto;
    }

    .register-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        background: #fff;
    }

    .step-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }

    .step-active {
        background: #0d1b3d;
        color: #fff;
    }

    .step-done {
        background: #198754;
        color: #fff;
    }

    .step-pending {
        background: #e9ecef;
        color: #6c757d;
    }

    .upload-box {
        border: 2px dashed #ced4da;
        border-radius: 16px;
        padding: 20px;
        background: #fff;
    }

    .section-title {
        font-weight: 700;
        color: #0d1b3d;
    }

    .register-hero {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        border-radius: 24px;
        padding: 40px 30px;
        margin-bottom: 30px;
    }

    .register-hero h1 {
        font-weight: 800;
        margin-bottom: 10px;
    }

    .register-hero p {
        margin-bottom: 0;
        color: rgba(255,255,255,0.82);
    }

    .register-progress-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    }

    @media (max-width: 768px) {
        .register-hero {
            padding: 28px 20px;
            text-align: center;
        }
    }
</style>
@endpush

@section('user-content')
<div class="container vendor-register-page">
    <div class="register-wrapper">

        <div class="register-hero">
            <h1>Vendor Registration</h1>
            <p>Join LasaWheels as a verified vehicle provider and grow your rental business.</p>
        </div>

        @include('user.pages.vendor-register.partials.progress', ['currentStep' => $currentStep ?? 1])

        @if(session('success'))
            <div class="alert alert-success rounded-4 mt-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger rounded-4 mt-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card register-card mt-4">
            <div class="card-body p-4 p-md-5">
                @yield('register-content')
            </div>
        </div>

    </div>
</div>
@endsection
