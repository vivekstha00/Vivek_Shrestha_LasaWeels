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
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
        });
    </script>
</body>
</html>
