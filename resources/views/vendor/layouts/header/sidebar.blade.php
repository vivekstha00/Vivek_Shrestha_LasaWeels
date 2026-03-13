<aside class="vendor-sidebar position-fixed top-0 start-0 h-100 p-3">
    <a href="{{ route('vendor.dashboard') }}" class="brand d-flex align-items-center gap-2 text-dark mb-3 px-2">
        <div>
            <div class="fw-bold">LasaWheels</div>
            <small class="text-muted">Vendor Panel</small>
        </div>
    </a>

    <ul class="nav nav-pills flex-column vendor-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}"
               href="{{ route('vendor.dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mt-1">
            <a class="nav-link {{ request()->routeIs('vendor.vehicles.*') ? 'active' : '' }}"
               href="{{ route('vendor.vehicles.index') }}">
                <i class="fas fa-car-side me-2"></i> Vehicles
            </a>
        </li>

        <li class="nav-item mt-1">
            <a class="nav-link {{ request()->routeIs('vendor.bookings.*') ? 'active' : '' }}"
            href="{{ route('vendor.bookings.index') }}">
                <i class="fas fa-calendar-check me-2"></i> Bookings
            </a>
        </li>
        <li class="nav-item mt-1">
            <a class="nav-link {{ request()->routeIs('vendor.users.*') ? 'active' : '' }}"
            href="{{ route('vendor.users.index') }}">
                <i class="fas fa-users me-2"></i> Users
            </a>
        </li>
        <li class="nav-item mt-1">
            <a class="nav-link {{ request()->routeIs('vendor.drivers.*') ? 'active' : '' }}"
            href="{{ route('vendor.drivers.index') }}">
                <i class="fas fa-id-card me-2"></i> Drivers
            </a>
        </li>
        <li class="nav-item mt-1">
            <a class="nav-link disabled" href="#">
                <i class="fas fa-chart-line me-2"></i> Reports
            </a>
        </li>
        <li class="nav-item mt-1"></li>
            <a class="nav-link {{ request()->routeIs('vendor.payments.*') ? 'active' : '' }}"
            href="{{ route('vendor.payments.index') }}">
                <i class="fas fa-money-check-alt me-2"></i> Payments
            </a>
        </li>

    </ul>

    <div class="mt-auto pt-3">
        <form class="mt-2" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100 mt-2">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</aside>
