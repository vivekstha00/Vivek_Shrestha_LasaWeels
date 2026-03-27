<aside class="vendor-sidebar">
    <a href="{{ route('vendor.dashboard') }}" class="brand">
        <div>
            <div class="fw-bold">LasaWheels</div>
            <small class="text-muted">Vendor Panel</small>
        </div>
    </a>

    <ul class="nav nav-pills flex-column vendor-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}" href="{{ route('vendor.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.vehicles.*') ? 'active' : '' }}" href="{{ route('vendor.vehicles.index') }}">
                <i class="fas fa-car-side"></i> Vehicles
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.bookings.*') ? 'active' : '' }}" href="{{ route('vendor.bookings.index') }}">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.users.*') ? 'active' : '' }}" href="{{ route('vendor.users.index') }}">
                <i class="fas fa-users"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.drivers.*') ? 'active' : '' }}" href="{{ route('vendor.drivers.index') }}">
                <i class="fas fa-id-card"></i> Drivers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.payments.*') ? 'active' : '' }}" href="{{ route('vendor.payments.index') }}">
                <i class="fas fa-money-bill-wave"></i> Payments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.subscriptions.*') ? 'active' : '' }}" href="{{ route('vendor.subscriptions.index') }}">
                <i class="fas fa-bell"></i> Subscriptions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.reviews.*') ? 'active' : '' }}" href="{{ route('vendor.reviews.index') }}">
                <i class="fas fa-star"></i> Reviews
            </a>
        </li>
    </ul>

    <div class="logout-btn">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</aside>
