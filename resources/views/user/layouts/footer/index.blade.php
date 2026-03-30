<footer class="mt-5" style="background:#1f1f1f; color:#fff;">

    <div class="container py-5">
        <div class="row g-5 align-items-start">

            {{-- About / Logo --}}
            <div class="col-lg-4 col-md-6">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm"
                    style="width: 120px; height: 120px;">
                    <img src="{{ asset('images/logo.png') }}"
                        alt="LasaWheels Logo"
                        style="max-width: 85px; height: auto;">
                </div>

                <h4 class="fw-bold mb-3">About Us</h4>
                <p class="text-light mb-0" style="line-height: 1.8;">
                    LasaWheels is a smart vehicle rental platform that helps users search,
                    compare and book cars, bikes and scooters easily through one system.
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-4 col-md-6">
                <h4 class="fw-bold mb-3">Quick Links</h4>
                <ul class="list-unstyled mb-3">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-decoration-none text-light">Home</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('about') }}" class="text-decoration-none text-light">About Us</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('blog.index') }}" class="text-decoration-none text-light">Blogs</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('contact.create') }}" class="text-decoration-none text-light">Contact</a>
                    </li>
                </ul>

                <div class="d-flex gap-3 mt-4">
                    <a href="https://www.facebook.com"
                       target="_blank"
                       class="d-inline-flex align-items-center justify-content-center rounded-circle text-white text-decoration-none"
                       style="width:46px; height:46px; background:#3b5998;">
                        <i class="fab fa-facebook-f"></i>
                    </a>

                    <a href="https://www.instagram.com"
                       target="_blank"
                       class="d-inline-flex align-items-center justify-content-center rounded-circle text-white text-decoration-none"
                       style="width:46px; height:46px; background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);">
                        <i class="fab fa-instagram"></i>
                    </a>

                    <a href="https://www.linkedin.com"
                       target="_blank"
                       class="d-inline-flex align-items-center justify-content-center rounded-circle text-white text-decoration-none"
                       style="width:46px; height:46px; background:#0077b5;">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            {{-- Office Location --}}
            <div class="col-lg-4 col-md-12">
                <h4 class="fw-bold mb-3">Office Location</h4>

                <p class="mb-3">
                    <i class="fa-solid fa-location-dot me-2"></i>
                    Pokhara, Nepal
                </p>

                <p class="mb-3">
                    <i class="fa-solid fa-phone me-2"></i>
                    +977-98XXXXXXXX
                </p>

                <p class="mb-0">
                    <i class="fa-solid fa-envelope me-2"></i>
                    info@lasawheels.com
                </p>
            </div>

        </div>
    </div>

    <div style="background:#111827; border-top:1px solid rgba(255,255,255,0.08);">
        <div class="container text-center py-3">
            <small class="text-white-50 fw-semibold">
                Copyright © {{ date('Y') }} LasaWheels | All Rights Reserved
            </small>
        </div>
    </div>
</footer>
