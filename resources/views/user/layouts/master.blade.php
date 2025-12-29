<!DOCTYPE html>
<html lang="en">
@include('user.layouts.header.index')

<body>
    @include('user.layouts.header.navbar')

    <main class="py-4">
        @yield('user-content')
    </main>

    @include('user.layouts.footer.index')
</body>
</html>
