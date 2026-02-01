<nav class="navbar navbar-expand-lg navbar-dark site-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">LasaWheels</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- LEFT MENU --}}
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                       href="{{ route('home') }}">
                        Home
                    </a>
                </li>

                {{-- Corporate Rent / Vendor --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('corporate.rent') ? 'active' : '' }}"
                       href="{{ route('corporate.rent') }}">
                        Corporate Rent
                    </a>
                </li>
            </ul>

            {{-- RIGHT MENU --}}
            <div class="d-flex gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-light">
                        Login
                    </a>

                    <a href="{{ route('register') }}" class="btn btn-main">
                        Sign Up
                    </a>
                @else
                    <span class="text-white d-flex align-items-center me-2">
                        Hi, <strong class="ms-1">{{ Auth::user()->name }}</strong>
                    </span>

                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light">
                            Admin
                        </a>
                    @elseif(Auth::user()->role === 'vendor')
                        <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-light">
                            Vendor
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">
                            Logout
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>
