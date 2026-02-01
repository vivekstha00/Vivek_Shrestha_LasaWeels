<div class="admin-sidebar vh-100 position-fixed p-3">
    <h5 class="text-uppercase border-bottom pb-2">Admin Menu</h5>

    <ul class="nav flex-column">
        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </a>
        </li>

        {{-- Manage Users --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="fas fa-users me-2"></i>
                Manage Users
            </a>
        </li>

        {{-- Manage Vendors --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}"
               href="{{ route('admin.vendors.index') }}">
                <i class="fas fa-building me-2"></i>
                Manage Vendors
            </a>
        </li>
    </ul>
</div>
