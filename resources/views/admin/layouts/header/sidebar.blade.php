<div class="sidebar collapse d-md-block" id="adminSidebar">
    <h5 class="mb-4">LasaWheels</h5>

    <ul class="nav flex-column flex-grow-1">
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <i class="fa-solid fa-users"></i> Users
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}" href="{{ route('admin.vendors.index') }}">
                <i class="fa-solid fa-building"></i> Vendors
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.vendor-subscriptions.*') ? 'active' : '' }}" href="{{ route('admin.vendor-subscriptions.index') }}">
                <i class="fa-solid fa-user-shield"></i> Vendor Subscriptions
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}" href="{{ route('admin.vehicles.index') }}">
                <i class="fa-solid fa-car"></i> Vehicles
            </a>
        </li>
        @php
            $financeActive = request()->routeIs('admin.discount-codes.*')
                || request()->routeIs('admin.payments.*')
                || request()->routeIs('admin.refunds.*');
        @endphp
        <li class="nav-item mb-1">
            <a class="nav-link d-flex justify-content-between align-items-center {{ $financeActive ? 'active' : '' }}"
               data-bs-toggle="collapse"
               href="#adminFinanceMenu"
               role="button"
               aria-expanded="{{ $financeActive ? 'true' : 'false' }}"
               aria-controls="adminFinanceMenu">
                <span><i class="fa-solid fa-wallet"></i> Finance</span>
                <i class="fa-solid fa-chevron-down small"></i>
            </a>

            <div class="collapse {{ $financeActive ? 'show' : '' }}" id="adminFinanceMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li class="nav-item mb-1">
                        <a class="nav-link py-1 {{ request()->routeIs('admin.discount-codes.*') ? 'active' : '' }}" href="{{ route('admin.discount-codes.index') }}">
                            <i class="fa-solid fa-tags"></i> Discount Codes
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link py-1 {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
                            <i class="fa-solid fa-money-bill-wave"></i> Payments
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link py-1 {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}" href="{{ route('admin.refunds.index') }}">
                            <i class="fa-solid fa-arrow-rotate-left"></i> Refund Requests
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.subscription-plans.*') ? 'active' : '' }}" href="{{ route('admin.subscription-plans.index') }}">
                <i class="fa-solid fa-list"></i> Subscription Plans
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">
                <i class="fa-solid fa-blog"></i> Blog Posts
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}">
                <i class="fa-solid fa-envelope"></i> Contact Requests
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                <i class="fa-solid fa-star"></i> Reviews
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                <i class="fa-solid fa-chart-column"></i> Reports
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link {{ request()->routeIs('admin.loyalty.*') ? 'active' : '' }}" href="{{ route('admin.loyalty.index') }}">
                <i class="fa-solid fa-gift"></i> Loyalty
            </a>
        </li>
    </ul>

    <!-- Logout at bottom -->
    <div class="logout-btn">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>

</div>
