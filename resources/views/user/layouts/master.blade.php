<!DOCTYPE html>
<html lang="en">
@include('user.layouts.header.index')

<body>
    @include('user.layouts.header.navbar')

    <main class="pt-4" style="margin-top: 90px;">
        @yield('user-content')
    </main>

    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container text-center">
            <p class="mb-2">© {{ date('Y') }} LasaWheels. All rights reserved.</p>
            <p class="small opacity-75">Simple, reliable, and affordable vehicle rental in Nepal.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.addEventListener('scroll', () => {
            document.getElementById('navbar')?.classList.toggle('solid', window.scrollY > 100);
        });
    </script>

    @stack('scripts')
</body>
</html>
