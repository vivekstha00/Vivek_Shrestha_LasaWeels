<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LasaWheels - Auth')</title>

    @php($faviconVersion = @filemtime(public_path('favicon.ico')) ?: time())
    @php($logoVersion = @filemtime(public_path('images/logo.png')) ?: time())
    @php($hasToastNotifications = session()->has('success') || session()->has('error') || $errors->any())
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo.png') }}?v={{ $logoVersion }}">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    @if($hasToastNotifications)
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if($hasToastNotifications)
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    @endif

    @stack('styles')

    <style>
        html,
        body {
            min-height: 100%;
            margin: 0;
        }

        body {
            background-color: #0f172a;
            background-image: linear-gradient(120deg, rgba(15, 23, 42, 0.86), rgba(15, 23, 42, 0.64));
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        @media (min-width: 992px) {
            body {
                background-image: linear-gradient(120deg, rgba(15, 23, 42, 0.78), rgba(15, 23, 42, 0.40)), url('{{ asset('images/ford.jpeg') }}');
            }
        }

        main {
            min-height: 100vh;
        }

        .auth-page {
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: transparent;
        }

        .auth-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: transparent;
            pointer-events: none;
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
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid rgba(148, 163, 184, 0.34);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.28);
        }

        .auth-card-wrap .card-body,
        .auth-card-wrap .form-label,
        .auth-card-wrap p,
        .auth-card-wrap h3 {
            color: #f8fafc;
        }

        .auth-card-wrap a {
            color: #93c5fd;
        }

        .auth-card-wrap a:hover {
            color: #bfdbfe;
        }

        .auth-card-wrap .form-control,
        .auth-card-wrap .input-group-text {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(148, 163, 184, 0.42);
            color: #f8fafc;
        }

        .auth-card-wrap .form-control::placeholder {
            color: rgba(248, 250, 252, 0.72);
        }

        .auth-card-wrap .form-control:focus {
            background: rgba(255, 255, 255, 0.18);
            border-color: #60a5fa;
            color: #f8fafc;
            box-shadow: 0 0 0 0.2rem rgba(96, 165, 250, 0.18);
        }

        .auth-card-wrap .input-group-text {
            cursor: pointer;
        }

        .auth-card-wrap .invalid-feedback {
            color: #fecaca;
        }

        .btn-main {
            background-color: #16a34a;
            border: 1px solid #16a34a;
            color: #fff;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-main:hover,
        .btn-main:focus,
        .btn-main:active {
            background-color: #15803d;
            border-color: #15803d;
            color: #fff;
        }

        .btn-main:focus {
            box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.2);
        }

        @media (max-width: 991.98px) {
            .auth-page {
                padding: 1.25rem 0.75rem;
            }
        }
    </style>
</head>

<body class="min-vh-100">
    <main>
        @yield('user-content')
    </main>

    @if($hasToastNotifications)
        <script defer src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @endif

    <script>
        window.showNotification = function (type, message) {
            if (!message) {
                return;
            }

            if (typeof toastr !== 'undefined') {
                toastr[type](message);
                return;
            }

            alert(message);
        };

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    newestOnTop: true,
                    preventDuplicates: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                };
            }

            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));
            const errorMessages = @json($errors->all());

            if (successMessage) {
                window.showNotification('success', successMessage);
            }

            if (errorMessage) {
                window.showNotification('error', errorMessage);
            }

            [...new Set(errorMessages || [])].forEach((message) => {
                window.showNotification('error', message);
            });

            document.body.addEventListener('click', function (event) {
                const trigger = event.target.closest('.input-group-text');
                if (!trigger) {
                    return;
                }

                const group = trigger.closest('.input-group');
                const input = group ? group.querySelector('input.password-input') : null;
                const icon = group ? group.querySelector('.password-toggle') : null;

                if (!input) {
                    return;
                }

                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');

                if (icon) {
                    icon.classList.toggle('bi-eye', !isPassword);
                    icon.classList.toggle('bi-eye-slash', isPassword);
                }
            });
        });
    </script>

    @stack('scripts')
    @stack('js')
</body>
</html>
