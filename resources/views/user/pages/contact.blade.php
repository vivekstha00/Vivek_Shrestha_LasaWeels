@extends('user.layouts.master')

@section('user-content')
<div class="container py-0 mt-0">
    <div class="text-center mb-4 pt-4">
        <h2 class="fw-bold mb-0">Contact Us</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 border-0 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4 align-items-stretch">
        <div class="col-lg-7">
            <h4 class="mb-3 fw-semibold">Get in Touch</h4>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Main Office</h6>
                    <p class="mb-2"><strong>Phone:</strong> {{ $office['phone'] ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Email:</strong> {{ $office['email'] ?? 'N/A' }}</p>
                    <p class="mb-3"><strong>Location:</strong> {{ $office['address'] ?? 'N/A' }}</p>

                    @if(!empty($office['latitude']) && !empty($office['longitude']))
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden border">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $office['latitude'] }},{{ $office['longitude'] }}&z=14&output=embed"
                                style="border:0;"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h4 class="mb-3 fw-semibold">Contact Support Form</h4>

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

                        <button class="btn btn-dark px-4">Submit Query</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 fw-semibold">Verified Vendors</h4>
        </div>

        @if($vendors->isNotEmpty())
            <div class="vendor-scroll d-flex gap-3 overflow-auto pb-2">
                @foreach($vendors as $vendor)
                    <a href="{{ route('contact.vendor.show', $vendor) }}" class="text-decoration-none text-dark flex-shrink-0" style="width: 290px;">
                        <div class="card shadow-sm border-0 rounded-4 vendor-card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h5 class="fw-bold mb-0 text-truncate" style="max-width: 180px;">
                                        {{ $vendor->vendorProfile->business_name ?? $vendor->name }}
                                    </h5>
                                    <span class="badge bg-success-subtle text-success border">Verified</span>
                                </div>

                                <p class="text-muted mb-2 small">
                                    {{ ucfirst($vendor->vendorProfile->business_type ?? 'vendor') }}
                                </p>

                                <p class="mb-2 small"><strong>Phone:</strong>
                                    {{ $vendor->vendorProfile->phone ?? $vendor->phone ?? 'N/A' }}
                                </p>

                                <p class="mb-0 small"><strong>Address:</strong>
                                    {{ $vendor->vendorProfile->business_address ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="alert alert-light border rounded-4 mb-0">
                No approved vendors available right now.
            </div>
        @endif
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

    .vendor-scroll {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
    }

    .vendor-scroll::-webkit-scrollbar {
        height: 8px;
    }

    .vendor-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 99px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.1);
        border-color: #6c757d;
    }

</style>
@endsection
