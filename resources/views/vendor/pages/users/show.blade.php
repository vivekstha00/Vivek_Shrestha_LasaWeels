@extends('vendor.layouts.master')

@section('title', 'User Details')
@section('page_title', 'User Details')
@section('page_subtitle', 'Full customer information')

@section('vendor-content')
<div class="mb-5">
    <a href="{{ route('vendor.users.index') }}" class="btn btn-outline-secondary">
        ← Back to Users
    </a>
</div>

<div class="row g-5">
    <!-- Left: Profile Card -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body text-center py-5">
                <div class="mx-auto mb-4 rounded-circle bg-light d-flex align-items-center justify-content-center"
                     style="width: 140px; height: 140px; border: 6px solid #f1f5f9;">
                    <i class="fas fa-user text-muted" style="font-size: 4.2rem;"></i>
                </div>

                <h3 class="fw-bold mb-2">{{ $user->name }}</h3>
                <p class="text-muted mb-4">{{ $user->email }}</p>

                <div class="d-flex justify-content-center gap-2 mb-5">
                    <span class="badge bg-success px-3 py-2">Account Active</span>
                    @if($user->email_verified_at)
                        <span class="badge bg-primary px-3 py-2">Email Verified</span>
                    @else
                        <span class="badge bg-secondary px-3 py-2">Email Not Verified</span>
                    @endif
                </div>

                <div class="border-top pt-4">
                    <div class="row text-start g-4">
                        <div class="col-6">
                            <div class="small text-muted mb-1">Contact</div>
                            <div class="fw-semibold">{{ $user->phone ?? 'Not Provided' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted mb-1">Join Date</div>
                            <div class="fw-semibold">{{ optional($user->created_at)->format('d M Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted mb-1">Document Status</div>
                            <div class="fw-semibold">{{ $docStatus ?? 'Not Verified' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted mb-1">Total Bookings</div>
                            <div class="fw-semibold">{{ $totalBookings ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Detailed Information -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4">All Details</h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="small text-muted">User ID</div>
                        <div class="fw-semibold fs-5">#{{ $user->id }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Role</div>
                        <div class="fw-semibold fs-5 text-capitalize">{{ $user->role }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Account Status</div>
                        <span class="badge bg-success fs-6">{{ $user->status ?? 'Approved' }}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Email Verified</div>
                        <span class="badge {{ $user->email_verified_at ? 'bg-success' : 'bg-secondary' }} fs-6">
                            {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>

                <hr class="my-5">

                <h6 class="fw-bold mb-3">Additional Notes</h6>
                <div class="bg-light p-4 rounded-3">
                    @if(!empty($latestBooking))
                        @php
                            $vehicleName = trim(($latestBooking->vehicle->brand ?? '') . ' ' . ($latestBooking->vehicle->model ?? ''));
                        @endphp
                        <div class="small text-muted mb-1">Latest Booking</div>
                        <div class="fw-semibold">#{{ $latestBooking->id }}</div>
                        <div class="text-muted small">{{ $vehicleName ?: ($latestBooking->vehicle->title ?? 'N/A') }}</div>
                        <div class="text-muted small">Pickup: {{ \Carbon\Carbon::parse($latestBooking->pickup_datetime)->format('d M Y, h:i A') }}</div>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <span class="badge bg-light text-dark text-capitalize">{{ $latestBooking->status }}</span>
                            <span class="badge {{ ($latestBooking->payment_status ?? 'unpaid') === 'paid' ? 'bg-success' : (($latestBooking->payment_status ?? 'unpaid') === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }} text-capitalize">
                                {{ $latestBooking->payment_status ?? 'unpaid' }}
                            </span>
                        </div>
                    @else
                        <p class="text-muted mb-0">No bookings yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
