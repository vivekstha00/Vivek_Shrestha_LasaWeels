<div class="sidebar p-3">
    <h5 class="text-white fw-bold mb-3">LasaWheels</h5>
    <p class="text-white-50 small mb-4">Super Admin</p>

    <ul class="nav flex-column">
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-chart-line me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="fa-solid fa-users me-2"></i> Users
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}"
               href="{{ route('admin.vendors.index') }}">
                <i class="fa-solid fa-building me-2"></i> Vendors
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}"
               href="{{ route('admin.vehicles.index') }}">
                <i class="fa-solid fa-circle-check me-2"></i> Verifications
            </a>
        </li>
    </ul>
</div>
