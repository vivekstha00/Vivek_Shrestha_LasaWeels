@extends('user.layouts.master')

@section('user-content')
<div class="container py-5 mt-5">

    <div class="text-center mb-4">
        <h2 class="fw-bold mb-1">Contact Us</h2>
        <p class="text-muted mb-0">Send your query or connect directly with verified vendors</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <h4 class="mb-3 fw-semibold">Contact Support</h4>

            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-light border-0 rounded-top-4 py-3">
                    <h6 class="mb-0 fw-semibold">Submit a Query</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="98XXXXXXXX" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Enter your subject" value="{{ old('subject') }}" required>
                            @error('subject') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message</label>
                            <textarea name="message" rows="5" class="form-control" placeholder="Write your query here..." required>{{ old('message') }}</textarea>
                            @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <button class="btn btn-dark px-4">
                            Submit Query
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h4 class="mb-3 fw-semibold">Verified Vendors</h4>

            @forelse($vendors as $vendor)
                <div class="card shadow-sm border-0 rounded-4 mb-3 vendor-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h5 class="fw-bold mb-0">
                                {{ $vendor->vendorProfile->business_name ?? $vendor->name }}
                            </h5>
                            <span class="badge bg-success-subtle text-success border">Verified</span>
                        </div>

                        <p class="text-muted mb-2 small">
                            {{ ucfirst($vendor->vendorProfile->business_type ?? 'vendor') }}
                        </p>

                        <p class="mb-2 small">
                            <strong>Phone:</strong>
                            {{ $vendor->vendorProfile->phone ?? $vendor->phone ?? 'N/A' }}
                        </p>

                        <p class="mb-3 small">
                            <strong>Address:</strong>
                            {{ $vendor->vendorProfile->business_address ?? 'N/A' }}
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            @if(!empty($vendor->vendorProfile->latitude) && !empty($vendor->vendorProfile->longitude))
                                <a href="https://www.google.com/maps?q={{ $vendor->vendorProfile->latitude }},{{ $vendor->vendorProfile->longitude }}"
                                   target="_blank"
                                   class="btn btn-outline-dark btn-sm">
                                    View Map
                                </a>

                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $vendor->vendorProfile->latitude }},{{ $vendor->vendorProfile->longitude }}"
                                   target="_blank"
                                   class="btn btn-dark btn-sm">
                                    Get Directions
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-light border rounded-4">
                    No approved vendors available right now.
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .vendor-card {
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .vendor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.1);
        border-color: #6c757d;
    }
</style>
@endsection
