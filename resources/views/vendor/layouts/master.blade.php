<!DOCTYPE html>
<html lang="en">
@include('vendor.layouts.header.index')

<body>

    @include('vendor.layouts.header.sidebar')

    <div class="flex-grow-1 d-flex flex-column" style="margin-left: 280px;">
        @include('vendor.layouts.header.navbar')

        <main class="p-4">
            @yield('vendor-content')
        </main>
    </div>

    @include('vendor.layouts.footer.index')
</body>
</html>
