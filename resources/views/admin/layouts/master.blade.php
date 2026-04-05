<!DOCTYPE html>
<html lang="en">
@include('admin.layouts.header.index')

<body>
    @include('admin.layouts.header.sidebar')

    <div class="main-content">
        @include('admin.layouts.header.navbar')

        <main>
            @yield('admin-content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
        });

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
            const warningMessage = @json(session('warning'));
            const infoMessage = @json(session('info'));
            const errorMessages = @json($errors->all());

            window.showNotification('success', successMessage);
            window.showNotification('error', errorMessage);
            window.showNotification('warning', warningMessage);
            window.showNotification('info', infoMessage);

            [...new Set(errorMessages || [])].forEach((message) => {
                window.showNotification('error', message);
            });
        });
    </script>
    @stack('scripts')
    @stack('js')
</body>
</html>
