<nav class="vendor-topbar navbar navbar-light px-4 py-3">
    <div class="container-fluid px-0">
        <div>
            <div class="fw-bold fs-5 mb-0">@yield('page_title', 'Dashboard')</div>
            <small class="text-muted">@yield('page_subtitle', 'Overview of your vehicle rental business')</small>
        </div>

        <div class="dropdown">
            <button class="btn p-0 border-0 bg-transparent d-flex align-items-center gap-2"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;background:#000;color:#fff;font-weight:600;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'V', 0, 1)) }}
                </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm p-2" style="min-width: 220px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('vendor.profile') }}">
                        <i class="bi bi-person"></i> Profile
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item d-flex align-items-center gap-2 text-danger" type="submit">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
