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
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
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

                        <button type="submit" class="btn btn-main w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
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
    $(document).ready(function () {
        function showErrors(errors) {
            [...new Set(errors)].forEach((message) => {
                if (typeof window.showNotification === 'function') {
                    window.showNotification('error', message);
                }
            });
        }

        $('body').on('click', '.password-toggle', function () {
            const $icon = $(this);
            const $input = $icon.closest('.input-group').find('.password-input');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });

        $('#loginForm').on('submit', function (event) {
            const email = $('#email').val()?.trim() || '';
            const password = $('#password').val() || '';
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
