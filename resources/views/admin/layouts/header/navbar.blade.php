<nav class="navbar navbar-light bg-white border-bottom px-3">
    <div class="container-fluid">
        <span class="fw-bold">Admin Panel</span>

        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                Hi, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong>
            </span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-dark">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>
