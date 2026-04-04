@extends('user.layouts.auth')

@section('user-content')
<div class="auth-page">
    <div class="auth-form-pane">
        <div class="auth-card-wrap">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h3 class="text-center mb-2">Verify OTP</h3>
                    <p class="text-center text-light-emphasis mb-4">
                        We sent a code to <strong>{{ $email }}</strong>
                    </p>

                    <form method="POST" action="{{ route('password.otp.verify') }}" novalidate>
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-3">
                            <label for="otp" class="form-label">
                                <i class="fas fa-key me-2"></i>6-digit OTP
                            </label>
                            <input
                                type="text"
                                name="otp"
                                id="otp"
                                class="form-control @error('otp') is-invalid @enderror"
                                value="{{ old('otp') }}"
                                maxlength="6"
                                placeholder="Enter OTP"
                                required
                            >
                            @error('otp')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-main w-100 mb-3">
                            Verify OTP
                        </button>
                    </form>

                    <div class="d-flex justify-content-between small">
                        @auth
                            <a href="{{ route('user.profile') }}" class="text-decoration-none">Back to Profile</a>
                        @else
                            <a href="{{ route('password.forgot') }}" class="text-decoration-none">Use another email</a>
                        @endauth
                        <a href="{{ route('password.otp.form', ['email' => $email]) }}" class="text-decoration-none">Refresh page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
