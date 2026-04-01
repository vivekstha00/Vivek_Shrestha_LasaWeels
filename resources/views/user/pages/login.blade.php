@extends('user.layouts.auth')

@push('styles')
    <style>
        .auth-page {
            min-height: 100vh;
            background-image: url('{{ asset('images/ford.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(15, 23, 42, 0.70), rgba(15, 23, 42, 0.45));
        }

        .auth-form-pane {
            position: relative;
            z-index: 1;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .auth-card-wrap {
            width: 100%;
            max-width: 520px;
        }

        .auth-card-wrap .card.card-soft {
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.28);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35);
        }

        .auth-card-wrap .card-body,
        .auth-card-wrap .form-label,
        .auth-card-wrap p,
        .auth-card-wrap h3 {
            color: #f8fafc;
        }

        .auth-card-wrap a {
            color: #dbeafe;
        }

        .auth-card-wrap .form-control,
        .auth-card-wrap .input-group-text {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.35);
            color: #f8fafc;
        }

        .auth-card-wrap .form-control::placeholder {
            color: rgba(248, 250, 252, 0.72);
        }

        .auth-card-wrap hr {
            border-color: rgba(255, 255, 255, 0.28);
        }

        .auth-card-wrap .invalid-feedback {
            color: #fecaca;
        }

        @media (max-width: 991.98px) {
            .auth-page {
                padding: 1.25rem 0.75rem;
            }
        }
    </style>
@endpush

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
                            <input type="email"
                                name="email"
                                id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                    class="password form-control @error('password') is-invalid @enderror">
                                <span class="input-group-text" style="cursor: pointer;">
                                    <i class="bi password-toggle bi-eye"></i>
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
                        <p class="mb-0">Don't have an account?
                            <a href="{{ route('register') }}" class="text-decoration-none">Register here</a>
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
    $(document).ready(function() {
        $('body').on('click', '.password-toggle', function() {
            if ($(this).hasClass('bi-eye')) {
                $(this).removeClass('bi-eye').addClass('bi-eye-slash');
                $('.password').attr('type', 'text');
            } else {
                $(this).removeClass('bi-eye-slash').addClass('bi-eye');
                $('.password').attr('type', 'password');
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
                [...new Set(errors)].forEach((message) => {
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('error', message);
                    }
                });
            }
        });
    });
</script>
@endpush
