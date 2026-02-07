<!DOCTYPE html>
<html lang="en">
@include('vendor.layouts.header.index')

<body>

    {{-- Sidebar --}}
    @include('vendor.layouts.header.sidebar')

    {{-- Content --}}
    <div class="vendor-content">
        @include('vendor.layouts.header.navbar')

        <main class="vendor-main p-4">
            @yield('vendor-content')
        </main>
    </div>

    @include('vendor.layouts.footer.index')
</body>
</html>
