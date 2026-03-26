@extends('user.layouts.master')

@section('user-content')
<div class="container py-5 mt-5">

    <div class="row g-4">
        <div class="col-lg-7">
            <h3 class="mb-4">Contact Support</h3>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" rows="5" class="form-control" required></textarea>
                        </div>

                        <button class="btn btn-dark">
                            Submit Query
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h3 class="mb-4">Verified Vendors</h3>

            @forelse($vendors as $vendor)
                <div class="card shadow-sm border-0 rounded-4 mb-3">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">
                            {{ $vendor->vendorProfile->business_name ?? $vendor->name }}
                        </h5>

                        <p class="text-muted mb-2">
                            {{ ucfirst($vendor->vendorProfile->business_type ?? 'vendor') }}
                        </p>

                        <p class="mb-2">
                            <strong>Phone:</strong>
                            {{ $vendor->vendorProfile->phone ?? $vendor->phone ?? 'N/A' }}
                        </p>

                        <p class="mb-3">
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
@endsection
