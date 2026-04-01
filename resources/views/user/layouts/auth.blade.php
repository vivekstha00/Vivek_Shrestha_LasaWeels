<!DOCTYPE html>
<html lang="en">
@include('user.layouts.header.index')

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
