<nav class="vendor-topbar navbar navbar-light px-4 py-3">
    <div class="container-fluid px-0">
        <div>
            <div class="fw-bold fs-5 mb-0">@yield('page_title', 'Dashboard')</div>
            <small class="text-muted">@yield('page_subtitle', 'Overview of your vehicle rental business')</small>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                Hi, <strong>{{ Auth::user()->name ?? 'Vendor' }}</strong>
            </span>
        </div>
    </div>
</nav>
