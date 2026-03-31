@extends('user.layouts.auth')

@push('styles')
    <style>
        .auth-split-page {
            min-height: 100vh;
            background: #85867F;
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
        <div class="col-lg-6 auth-image-pane" style="background-image: url('{{ asset('images/defender.jpg') }}');"></div>

        <div class="col-lg-6 auth-form-pane">
            <div class="auth-card-wrap">
                <div class="card card-soft">
                    <div class="card-body p-4">
                        <h3 class="mb-4"><i class="fas fa-user-plus me-2"></i>User Registration</h3>

                        <form action="{{ route('register.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password"
                                        class="password form-control @error('password') is-invalid @enderror" required>
                                    <span class="input-group-text" style="cursor: pointer;">
                                        <i class="bi password-toggle bi-eye"></i>
                                    </span>
                                </div>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-main w-100">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </button>

                            <div class="text-center mt-3">
                                <p class="mb-0">Already have an account?
                                    <a href="{{ route('login') }}" class="text-decoration-none">Back to Login</a>
                                </p>
                            </div>
                        </form>
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
