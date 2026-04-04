@extends('user.layouts.auth')

@section('user-content')
<div class="auth-page">
    <div class="auth-form-pane">
        <div class="auth-card-wrap">
            <div class="card card-soft">
                <div class="card-body p-4">
                    <h3 class="text-center mb-2">Reset Password</h3>
                    <p class="text-center text-light-emphasis mb-4">Set a new password for <strong>{{ $email }}</strong></p>

                    <form method="POST" action="{{ route('password.reset') }}" novalidate>
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>New Password
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
                                <i class="fas fa-lock me-2"></i>Confirm Password
                            </label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control password-input"
                                    required
                                >
                                <span class="input-group-text">
                                    <i class="bi bi-eye password-toggle"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-main w-100 mb-3">
                            Reset Password
                        </button>
                    </form>

                    <div class="text-center small">
                        @auth
                            <a href="{{ route('user.profile') }}" class="text-decoration-none">Back to Profile</a>
                        @else
                            <a href="{{ route('login') }}" class="text-decoration-none">Back to Login</a>
                        @endauth
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
    });
</script>
@endpush
