<!DOCTYPE html>
<html lang="en">
<head>
    @include('user.layouts.header.index')

    <link rel="preload" as="image" href="{{ asset('images/ford.jpg') }}" fetchpriority="high">

    @stack('styles')

    <style>
        html,
        body {
            min-height: 100%;
            margin: 0;
        }

        body {
            background-color: #0f172a;
            background-image: url('{{ asset('images/ford.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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
            background: linear-gradient(120deg, rgba(15, 23, 42, 0.78), rgba(15, 23, 42, 0.40));
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
            box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35);
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        window.showNotification = function (type, message) {
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
        });
    </script>

    @stack('scripts')
    @stack('js')
</body>
</html>
