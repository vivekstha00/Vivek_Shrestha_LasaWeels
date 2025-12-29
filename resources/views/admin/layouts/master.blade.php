<!DOCTYPE html>
<html lang="en">
@include('admin.layouts.header.index')

<body>
    @include('admin.layouts.header.sidebar')

    <div class="flex-grow-1 d-flex flex-column" style="margin-left: 180px;">
        @include('admin.layouts.header.navbar')

        <main class="p-4">
            @yield('admin-content')
        </main>
    </div>

    @include('admin.layouts.footer.index')
</body>
</html>
