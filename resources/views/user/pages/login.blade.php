@extends('user.layouts.auth')

@push('styles')
    <style>
        .auth-split-page {
            min-height: 100vh;
            background: #f3f4f6;
        }

        .auth-image-pane {
            min-height: 320px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .auth-image-pane::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.25));
        }

        .auth-form-pane {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-card-wrap {
            width: 100%;
            max-width: 520px;
        }

        @media (max-width: 991.98px) {
            .auth-form-pane {
                min-height: auto;
            }
        }
    </style>
@endpush

@section('user-content')
<div class="container-fluid px-0 auth-split-page">
    <div class="row g-0">
        <div class="col-lg-6 auth-image-pane" style="background-image: url('{{ asset('images/ford.jpg') }}');"></div>

        <div class="col-lg-6 auth-form-pane">
            <div class="auth-card-wrap">
                <div class="card card-soft">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Login</h3>
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <form method="POST" action="{{ route('login.store') }}">
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
    });
</script>
@endpush
