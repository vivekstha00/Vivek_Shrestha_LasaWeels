@extends('user.layouts.auth')

@section('user-content')
<div class="auth-page">
    <div class="auth-form-pane">
        <div class="auth-card-wrap">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Login</h3>

                    <form id="loginForm" method="POST" action="{{ route('login.store') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock me-2"></i>Password
                            </label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control password-input"
                                    required
                                >
                                <span class="input-group-text" style="cursor: pointer;">
                                    <i class="bi bi-eye password-toggle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="{{ route('password.forgot') }}" class="text-decoration-none">
                                <small>Forgot Password?</small>
                            </a>
                        </div>

                        <button type="submit" class="btn btn-main w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                        <hr>
                        <div class="text-center mb-3">
                            <a href="{{ route('google_redirect') }}" class="btn btn-outline-dark w-100">
                                <i class="bi bi-google me-2"></i>Sign in with Google
                            </a>
                        </div>
                    </form>

                    <hr>

                    <div class="text-center">
                        <p class="mb-0">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-decoration-none">
                                Register here
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        if (!loginForm || !emailInput || !passwordInput) {
            return;
        }

        function showErrors(errors) {
            [...new Set(errors)].forEach((message) => {
                if (typeof window.showNotification === 'function') {
                    window.showNotification('error', message);
                }
            });
        }

        loginForm.addEventListener('submit', function (event) {
            const email = (emailInput.value || '').trim();
            const password = passwordInput.value || '';
            const errors = [];

            if (!email) {
                errors.push('Email is required.');
            } else {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    errors.push('Please enter a valid email address.');
                }
            }

            if (!password) {
                errors.push('Password is required.');
            }

            if (errors.length > 0) {
                event.preventDefault();
                showErrors(errors);
            }
        });
    });
</script>
@endpush
