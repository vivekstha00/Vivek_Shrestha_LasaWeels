<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LasaWheels - Vehicle Rental System')</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/user.css') }}">

    <style>
        :root {
            --nav-h: 90px;
            --dark-bg: #0f172a;
            --green-accent: #22c55e;
            --green-dark: #16a34a;
            --text-light: #f1f5f9;
            --muted: #94a3b8;
        }

        body {
            font-family: system-ui, sans-serif;
            color: #020617;
            background: #f8fafc;
        }

        .site-navbar {
            background: transparent;
            transition: all 0.4s ease;
            z-index: 1000;
        }

        .site-main {
            margin-top: 90px;
        }

        .site-navbar.solid {
            background: white !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: rgb(0, 0, 0) !important;
        }

        .nav-link {
            color: rgb(0, 0, 0) !important;
            font-weight: 500;
            padding: 0.75rem 1.2rem !important;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--green-accent) !important;
        }

        .btn-signin {
            background: var(--green-accent);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.55rem 1.4rem;
            border-radius: 50px;
        }

        .btn-signin:hover {
            background: var(--green-dark);
        }

        .hero {
            background: linear-gradient(rgba(15,23,42,0.68), rgba(15,23,42,0.82)),
                        url('{{ asset('images/hero-cars-dark.jpg') }}') center/cover no-repeat fixed;
            min-height: 100vh;
            color: white;
            position: relative;
        }

        /* HERO: Bootstrap version of Tailwind h-screen bg-cover bg-center */
        .hero-home{
            min-height: 100vh;
            background-image: url('{{ asset('images/hero.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        /* dark overlay */
        .hero-home::before{
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.45);
        }

        /* push content down so navbar doesn't cover */
        .hero-home .hero-content{
            position: relative;
            z-index: 1;
            padding-top: var(--nav-h);
            padding-bottom: 40px;
        }


        .search-container {
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }

        .service-toggle {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            transition: all 0.3s;
            font-weight: 600;
        }

        .service-toggle.active {
            border-color: var(--green-accent);
            background: var(--green-accent);
            color: white;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 2.5rem 1.8rem;
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
        }

        .service-square {
            width: 150px;
            height: 150px;
            border: 2px solid #e5e7eb;
            border-radius: 18px;
            background: #ffffff;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            gap: 10px;
            font-weight: 600;
            font-size: 17px;

            transition: all .2s ease;
            cursor: pointer;
        }

        /* icon size */
        .service-square i {
            font-size: 28px;
            color: #16a34a;
        }

        /* hover */
        .service-square:hover {
            border-color: #22c55e;
            transform: translateY(-2px);
        }

        /* active state */
        .service-square.active {
            border-color: #22c55e;
            background: #f0fdf4;
        }

    </style>

    @stack('styles')
</head>
