<nav class="navbar navbar-expand-lg navbar-dark site-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">LasaWheels</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
            </ul>

            <div class="d-flex gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-main">Sign Up</a>
                @else
                    <span class="text-white d-flex align-items-center me-2">
                        Hi, <strong class="ms-1">{{ Auth::user()->name }}</strong>
                    </span>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>
