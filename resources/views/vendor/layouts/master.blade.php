<!DOCTYPE html>
<html lang="en">
@include('vendor.layouts.header.index')

<body>
    @include('vendor.layouts.header.sidebar')

    <div class="vendor-content">
        @include('vendor.layouts.header.navbar')

        <main class="vendor-main">
            @yield('vendor-content')
        </main>
    </div>

    @include('vendor.layouts.footer.index')
</body>
</html>
