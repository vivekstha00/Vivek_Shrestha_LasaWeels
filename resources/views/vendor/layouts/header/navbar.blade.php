<nav class="navbar navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="{{ route('vendor.dashboard') }}">LasaWheels Vendor</a>

    <div class="ms-auto d-flex align-items-center text-white">
        <span class="me-3">
            Hi, <strong>{{ Auth::user()->name ?? 'Vendor' }}</strong>
        </span>

        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
        </form>
    </div>
</nav>
