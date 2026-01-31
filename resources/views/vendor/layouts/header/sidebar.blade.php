<div class="bg-dark text-white vh-100 position-fixed p-3" style="width:280px;">
    <h5 class="text-uppercase border-bottom pb-2">Vendor Menu</h5>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('vendor.dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </li>

        {{-- Add later --}}
        {{-- <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="fas fa-car me-2"></i>My Vehicles
            </a>
        </li> --}}

        <li class="nav-item mt-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </li>
    </ul>
</div>
