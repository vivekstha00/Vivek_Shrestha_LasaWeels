<div class="sidebar p-3">
    <h5 class="text-white fw-bold mb-3">LasaWheels</h5>

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
            <a class="nav-link {{ request()->routeIs('admin.loyalty.*') ? 'active' : '' }}"
            href="{{ route('admin.loyalty.index') }}">
                <i class="fa-solid fa-gift me-2"></i> Loyalty
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.discount-codes.*') ? 'active' : '' }}"
            href="{{ route('admin.discount-codes.index') }}">
                <i class="fa-solid fa-tags me-2"></i> Discount Codes
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
                <i class="fa-solid fa-circle-check me-2"></i> Vehicles
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}"
            href="{{ route('admin.blog.index') }}">
                <i class="fa-solid fa-blog me-2"></i> Blog Posts
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}"
               href="{{ route('admin.contacts.index') }}">
                <i class="fa-solid fa-envelope me-2"></i> Contact Requests
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
               href="{{ route('admin.payments.index') }}">
                <i class="fa-solid fa-money-bill-transfer me-2"></i> Payments
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}"
               href="{{ route('admin.documents.index') }}">
                <i class="fa-solid fa-file me-2"></i> Documents
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"
               href="{{ route('admin.reviews.index') }}">
                <i class="fa-solid fa-star me-2"></i> Reviews
            </a>
        </li>
    </ul>
</div>
