<!DOCTYPE html>
<html lang="en">
@include('user.layouts.header.index')

<body class="d-flex flex-column min-vh-100">
    @include('user.layouts.header.navbar')

    <main class="pt-4 flex-grow-1" style="margin-top: 90px;">
        @yield('user-content')
    </main>

    @include('user.layouts.footer.index')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.addEventListener('scroll', () => {
            document.getElementById('navbar')?.classList.toggle('solid', window.scrollY > 100);
        });
    </script>

    @stack('scripts')
</body>
</html>
