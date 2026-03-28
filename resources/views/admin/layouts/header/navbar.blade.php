<nav class="navbar navbar-light">
    <div class="container-fluid px-0">
        <div class="d-flex align-items-center">
            <button class="btn btn-link text-dark me-3 d-md-none" type="button" id="sidebarToggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h5 class="fw-bold mb-0">Admin Panel</h5>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                Hi, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong>
            </span>
        </div>
    </div>
</nav>
