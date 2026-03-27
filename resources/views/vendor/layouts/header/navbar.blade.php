<nav class="vendor-topbar navbar navbar-light">
    <div class="container-fluid px-0">
        <div>
            <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
            <p class="page-subtitle">@yield('page_subtitle', 'Overview of your vehicle rental business')</p>
        </div>

        <div class="dropdown">
            <button class="btn p-0 border-0 bg-transparent d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown">
                <div class="profile-avatar">
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
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
