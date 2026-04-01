<!DOCTYPE html>
<html lang="en">
@include('user.layouts.header.index')

<body class="d-flex flex-column min-vh-100">
    @include('user.layouts.header.navbar')

    <main class="flex-grow-1 site-main">
        @yield('user-content')
    </main>

    @include('user.layouts.footer.index')

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

        window.addEventListener('scroll', () => {
            document.getElementById('navbar')?.classList.toggle('solid', window.scrollY > 100);
        });

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
            const errorMessages = @json($errors->all());

            if (successMessage) {
                window.showNotification('success', successMessage);
            }

            [...new Set(errorMessages || [])].forEach((message) => {
                window.showNotification('error', message);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
