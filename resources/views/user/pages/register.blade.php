@extends('user.layouts.auth')

@section('user-content')
<div class="auth-page">
    <div class="auth-form-pane">
        <div class="auth-card-wrap">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h3 class="mb-4">
                        <i class="bi bi-person-plus me-2"></i>User Registration
                    </h3>

                    <form id="registerForm" action="{{ route('register.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                Phone Number <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control @error('phone') is-invalid @enderror"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                required
                            >
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control password-input @error('password') is-invalid @enderror"
                                    required
                                >
                                <span class="input-group-text">
                                    <i class="bi bi-eye password-toggle"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control password-input @error('password_confirmation') is-invalid @enderror"
                                    required
                                >
                                <span class="input-group-text">
                                    <i class="bi bi-eye password-toggle"></i>
                                </span>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-main w-100">
                            <i class="bi bi-person-plus me-2"></i>Register
                        </button>

                        <div class="text-center mt-3">
                            <p class="mb-0">
                                Already have an account?
                                <a href="{{ route('login') }}" class="text-decoration-none">
                                    Back to Login
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        function showErrors(errors) {
            [...new Set(errors)].forEach((message) => {
                if (typeof window.showNotification === 'function') {
                    window.showNotification('error', message);
                }
            });
        }

        $('#registerForm').on('submit', function (event) {
            const name = $('#name').val()?.trim() || '';
            const email = $('#email').val()?.trim() || '';
            const phone = $('#phone').val()?.trim() || '';
            const password = $('#password').val() || '';
            const confirmPassword = $('#password_confirmation').val() || '';
            const errors = [];

            if (!name) {
                errors.push('Full Name is required.');
            }

            if (!email) {
                errors.push('Email Address is required.');
            } else {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    errors.push('Please enter a valid email address.');
                }
            }

            if (!phone) {
                errors.push('Phone Number is required.');
            } else if (!/^\d+$/.test(phone)) {
                errors.push('Phone Number must contain digits only.');
            } else if (phone.length < 10) {
                errors.push('Phone Number must be at least 10 digits.');
            } else if (phone.length > 15) {
                errors.push('Phone Number cannot be more than 15 digits.');
            }

            if (!password) {
                errors.push('Password is required.');
            } else if (password.length < 6) {
                errors.push('Password must be at least 6 characters.');
            }

            if (!confirmPassword) {
                errors.push('Confirm Password is required.');
            } else if (password !== confirmPassword) {
                errors.push('Password confirmation does not match.');
            }

            if (errors.length > 0) {
                event.preventDefault();
                showErrors(errors);
            }
        });
    });
</script>
@endpush
