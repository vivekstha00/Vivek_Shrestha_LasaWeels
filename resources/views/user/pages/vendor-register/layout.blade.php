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

        <div class="card register-card mt-4">
            <div class="card-body p-4 p-md-5">
                @yield('register-content')
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const registerForms = document.querySelectorAll('.register-card form');

        registerForms.forEach((form) => {
            form.setAttribute('novalidate', 'novalidate');

            form.addEventListener('submit', function (event) {
                const localErrors = [];
                const controls = form.querySelectorAll('input, select, textarea');

                controls.forEach((control) => {
                    if (control.disabled || control.type === 'hidden') {
                        return;
                    }

                    const labelText = form.querySelector(`label[for="${control.id}"]`)?.textContent?.trim()
                        || control.closest('.mb-3, .mb-4, .col-md-6, .col-md-12')?.querySelector('label')?.textContent?.trim()
                        || control.name?.replace(/_/g, ' ')
                        || 'This field';

                    const isRequired = control.hasAttribute('required');
                    const value = (control.value || '').trim();

                    if (isRequired && !value) {
                        localErrors.push(`${labelText} is required.`);
                    }

                    if (control.type === 'email' && value && !control.checkValidity()) {
                        localErrors.push('Please enter a valid email address.');
                    }

                    if (control.name === 'phone' && value) {
                        if (!/^\d+$/.test(value)) {
                            localErrors.push('Phone number must contain digits only.');
                        } else if (value.length < 10) {
                            localErrors.push('Phone number must be at least 10 digits.');
                        } else if (value.length > 10) {
                            localErrors.push('Phone number cannot be more than 10 digits.');
                        }
                    }
                });

                const password = form.querySelector('input[name="password"]')?.value || '';
                const passwordConfirmation = form.querySelector('input[name="password_confirmation"]')?.value || '';

                if (password && passwordConfirmation && password !== passwordConfirmation) {
                    localErrors.push('Password confirmation does not match.');
                }

                if (localErrors.length > 0) {
                    event.preventDefault();
                    [...new Set(localErrors)].forEach((message) => {
                        window.showNotification('error', message);
                    });
                }
            });
        });
    });
</script>
@endpush
