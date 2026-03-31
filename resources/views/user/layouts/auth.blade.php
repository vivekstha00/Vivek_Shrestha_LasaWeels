<!DOCTYPE html>
<html lang="en">
@include('user.layouts.header.index')

<body class="min-vh-100">
    <main>
        @yield('user-content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @stack('scripts')
    @stack('js')
</body>
</html>
