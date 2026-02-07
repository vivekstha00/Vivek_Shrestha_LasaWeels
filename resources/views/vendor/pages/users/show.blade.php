@extends('vendor.layouts.master')

@section('title', 'User Details - Vendor')
@section('page_title', 'User Details')
@section('page_subtitle', 'Full customer information')

@section('vendor-content')
<div class="container-fluid">

    <div class="mb-3">
        <a href="{{ route('vendor.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Users
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                             style="width:54px;height:54px;">
                            <i class="fas fa-user text-muted fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-5">{{ $user->name }}</div>
                            <div class="text-muted small">{{ $user->email }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="small text-muted">Contact</div>
                    <div class="fw-semibold">{{ $user->phone ?? '—' }}</div>

                    <div class="mt-3 small text-muted">Join Date</div>
                    <div class="fw-semibold">{{ optional($user->created_at)->format('M d, Y') ?? '—' }}</div>

                    <div class="mt-3 small text-muted">Document Status</div>
                    <div class="fw-semibold">{{ $docStatus }}</div>

                    <div class="mt-3 small text-muted">Total Bookings</div>
                    <div class="fw-semibold">{{ $totalBookings }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-soft">
                <div class="card-body">
                    <h6 class="mb-3">All Details</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">User ID</div>
                            <div class="fw-semibold">{{ $user->id }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Role</div>
                            <div class="fw-semibold">{{ $user->role }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Account Status</div>
                            <div class="fw-semibold">{{ $user->status ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Email Verified</div>
                            <div class="fw-semibold">
                                {{ $user->email_verified_at ? 'Yes' : 'No' }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-2">Notes (Optional)</h6>
                    <p class="text-muted mb-0">
                        This section can later show booking history, uploaded documents preview, and activity logs.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
