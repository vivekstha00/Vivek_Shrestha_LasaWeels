<!DOCTYPE html>
<html lang="en">
@include('admin.layouts.header.index')

<body class="bg-light">

    {{-- Sidebar --}}
    @include('admin.layouts.header.sidebar')

    {{-- Main content --}}
    <div class="main-content">
        {{-- Navbar --}}
        @include('admin.layouts.header.navbar')

        {{-- Page Content --}}
        <main class="container-fluid py-4">
            @yield('admin-content')
        </main>
    </div>

    @include('admin.layouts.footer.index')
</body>
</html>
