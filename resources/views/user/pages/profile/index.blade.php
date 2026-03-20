@extends('user.layouts.master')

@section('user-content')
<div class="container py-4">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-1">My Profile</h3>
        <p class="text-muted mb-0">Manage your profile, documents, and booking activity</p>
    </div>

    <div class="row g-4">

        {{-- LEFT SIDE --}}
        <div class="col-lg-4">

            {{-- Profile Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">

                    @if($user->profile_image)
                        <img
                            src="{{ asset('storage/'.$user->profile_image) }}"
                            alt="Profile Image"
                            class="rounded-circle border mb-3"
                            style="width:120px;height:120px;object-fit:cover;"
                        >
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                             style="width:120px;height:120px;font-size:40px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <div class="text-muted mb-2">{{ $user->email }}</div>
                    <div class="text-muted small">
                        Member since {{ $user->created_at->format('M Y') }}
                    </div>
                </div>
            </div>

            {{-- Update Profile --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Update Profile</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_image" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                value="{{ $user->email }}"
                                disabled
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="{{ old('phone', $user->phone) }}"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input
                                type="text"
                                name="address"
                                class="form-control"
                                value="{{ old('address', $user->address) }}"
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-3">
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="col-lg-8">

            {{-- Booking Stats --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center py-4">
                            <div class="text-muted small mb-1">Total Bookings</div>
                            <h3 class="fw-bold mb-0">{{ $totalBookings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center py-4">
                            <div class="text-muted small mb-1">Confirmed</div>
                            <h3 class="fw-bold text-primary mb-0">{{ $confirmedBookings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center py-4">
                            <div class="text-muted small mb-1">Completed</div>
                            <h3 class="fw-bold text-success mb-0">{{ $completedBookings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center py-4">
                            <div class="text-muted small mb-1">Cancelled</div>
                            <h3 class="fw-bold text-danger mb-0">{{ $cancelledBookings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loyalty Overview --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">Loyalty Points</h5>
                        <small class="text-muted">Earn points from completed trips and reviews</small>
                    </div>

                    @php
                        $tierClass = match(strtolower($loyaltyAccount->tier ?? 'bronze')) {
                            'gold' => 'bg-warning text-dark',
                            'silver' => 'bg-secondary',
                            default => 'bg-dark text-white',
                        };
                    @endphp

                    <span class="badge {{ $tierClass }} px-3 py-2 rounded-pill">
                        {{ ucfirst($loyaltyAccount->tier ?? 'bronze') }}
                    </span>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="border rounded-4 p-3 h-100 text-center">
                                <div class="small text-muted mb-1">Available Points</div>
                                <h4 class="fw-bold text-primary mb-0">{{ $loyaltyAccount->available_points ?? 0 }}</h4>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="border rounded-4 p-3 h-100 text-center">
                                <div class="small text-muted mb-1">Lifetime Earned</div>
                                <h4 class="fw-bold text-success mb-0">{{ $loyaltyAccount->lifetime_earned_points ?? 0 }}</h4>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="border rounded-4 p-3 h-100 text-center">
                                <div class="small text-muted mb-1">Redeemed</div>
                                <h4 class="fw-bold text-danger mb-0">{{ $loyaltyAccount->lifetime_redeemed_points ?? 0 }}</h4>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="border rounded-4 p-3 h-100 text-center">
                                <div class="small text-muted mb-1">Completed Trips</div>
                                <h4 class="fw-bold mb-0">{{ $loyaltyAccount->completed_bookings_count ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>

                    @php
                        $completedCount = $loyaltyAccount->completed_bookings_count ?? 0;
                        $currentTier = strtolower($loyaltyAccount->tier ?? 'bronze');

                        if ($currentTier === 'bronze') {
                            $nextTier = 'Silver';
                            $target = 5;
                        } elseif ($currentTier === 'silver') {
                            $nextTier = 'Gold';
                            $target = 10;
                        } else {
                            $nextTier = null;
                            $target = null;
                        }

                        $progress = $target ? min(($completedCount / $target) * 100, 100) : 100;
                    @endphp

                    @if($nextTier)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Progress to {{ $nextTier }}</span>
                                <span class="fw-semibold">{{ $completedCount }}/{{ $target }} trips</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <div class="fw-semibold">Gold Tier Achieved</div>
                            <div class="small">You have reached the highest loyalty level.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Loyalty Activity --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Recent Loyalty Activity</h5>
                </div>

                <div class="card-body px-4 pb-4">
                    @if(($loyaltyTransactions ?? collect())->isEmpty())
                        <div class="text-center py-4">
                            <h6 class="fw-bold mb-2">No loyalty activity yet</h6>
                            <p class="text-muted mb-0">Complete bookings and submit reviews to earn points.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Activity</th>
                                        <th>Booking</th>
                                        <th>Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loyaltyTransactions as $transaction)
                                        @php
                                            $typeLabel = match($transaction->type) {
                                                'earn_booking' => 'Trip Completed',
                                                'earn_first_booking_bonus' => 'First Booking Bonus',
                                                'earn_review_bonus' => 'Review Bonus',
                                                'redeem' => 'Redeemed',
                                                'restore_redemption' => 'Restored',
                                                'manual_adjustment' => 'Adjustment',
                                                default => ucfirst(str_replace('_', ' ', $transaction->type)),
                                            };

                                            $pointClass = $transaction->points >= 0 ? 'text-success' : 'text-danger';
                                        @endphp

                                        <tr>
                                            <td>{{ $transaction->created_at->format('d M Y') }}</td>
                                            <td>{{ $typeLabel }}</td>
                                            <td>{{ $transaction->booking_id ? '#'.$transaction->booking_id : '-' }}</td>
                                            <td class="fw-bold {{ $pointClass }}">
                                                {{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Personal Info --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Personal Information</h5>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Full Name</div>
                            <div class="fw-semibold">{{ $user->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Email</div>
                            <div class="fw-semibold">{{ $user->email }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Phone</div>
                            <div class="fw-semibold">{{ $user->phone ?: 'Not provided' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Address</div>
                            <div class="fw-semibold">{{ $user->address ?: 'Not provided' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Self Drive Verification Status --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Self-Drive Verification</h5>
                </div>

                <div class="card-body px-4 pb-4">
                    @if($selfDriveVerificationStatus === 'approved')
                        <div class="alert alert-success mb-0">
                            <div class="fw-semibold mb-1">Verified for Self Drive</div>
                            <div class="small">
                                Your license and citizenship are approved. You can book self-drive vehicles.
                            </div>
                        </div>
                    @elseif($selfDriveVerificationStatus === 'pending')
                        <div class="alert alert-warning mb-0">
                            <div class="fw-semibold mb-1">Verification Pending</div>
                            <div class="small">
                                Your documents are under review. You can still book vehicles with driver.
                            </div>
                        </div>
                    @elseif($selfDriveVerificationStatus === 'rejected')
                        <div class="alert alert-danger mb-0">
                            <div class="fw-semibold mb-1">Verification Rejected</div>
                            <div class="small">
                                One or more required documents were rejected. Please update and resubmit them.
                            </div>
                        </div>
                    @elseif($selfDriveVerificationStatus === 'expired')
                        <div class="alert alert-danger mb-0">
                            <div class="fw-semibold mb-1">License Expired</div>
                            <div class="small">
                                Your driving license has expired. Please upload a valid one to continue self-drive booking.
                            </div>
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <div class="fw-semibold mb-1">Documents Required</div>
                            <div class="small">
                                Upload your driving license and citizenship first to unlock self-drive booking.
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Documents --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Documents</h5>
                </div>

                <div class="card-body px-4 pb-4">
                    @php
                        $license = $documents['license'] ?? null;
                        $citizenship = $documents['citizenship'] ?? null;
                    @endphp

                    {{-- Driving License --}}
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">Driving License</h6>

                            @if($license)
                                <span class="badge
                                    {{ $license->status == 'approved' ? 'bg-success' :
                                       ($license->status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($license->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Not Uploaded</span>
                            @endif
                        </div>

                        @if($license)
                            <div class="mb-2 small text-muted">
                                Document Number: <span class="fw-semibold text-dark">{{ $license->document_number ?: 'N/A' }}</span>
                            </div>

                            <a href="{{ asset('storage/'.$license->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-3">
                                View Document
                            </a>
                        @endif

                        <form
                            action="{{ $license ? route('user.documents.update', $license) : route('user.documents.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            @if($license)
                                @method('PUT')
                            @endif

                            <input type="hidden" name="type" value="license">

                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="file" name="file" class="form-control form-control-sm" {{ $license ? '' : 'required' }}>
                                </div>

                                <div class="col-md-3">
                                    <input
                                        type="text"
                                        name="document_number"
                                        class="form-control form-control-sm"
                                        placeholder="License Number"
                                        value="{{ $license->document_number ?? '' }}"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <input
                                        type="date"
                                        name="issued_at"
                                        class="form-control form-control-sm"
                                        value="{{ $license->issued_at ?? '' }}"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <input
                                        type="date"
                                        name="expires_at"
                                        class="form-control form-control-sm"
                                        value="{{ $license->expires_at ?? '' }}"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Citizenship --}}
                    <div class="border rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">Citizenship</h6>

                            @if($citizenship)
                                <span class="badge
                                    {{ $citizenship->status == 'approved' ? 'bg-success' :
                                       ($citizenship->status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($citizenship->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Not Uploaded</span>
                            @endif
                        </div>

                        @if($citizenship)
                            <div class="mb-2 small text-muted">
                                Document Number: <span class="fw-semibold text-dark">{{ $citizenship->document_number ?: 'N/A' }}</span>
                            </div>

                            <a href="{{ asset('storage/'.$citizenship->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-3">
                                View Document
                            </a>
                        @endif

                        <form
                            action="{{ $citizenship ? route('user.documents.update', $citizenship) : route('user.documents.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            @if($citizenship)
                                @method('PUT')
                            @endif

                            <input type="hidden" name="type" value="citizenship">

                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="file" name="file" class="form-control form-control-sm" {{ $citizenship ? '' : 'required' }}>
                                </div>

                                <div class="col-md-4">
                                    <input
                                        type="text"
                                        name="document_number"
                                        class="form-control form-control-sm"
                                        placeholder="Citizenship Number"
                                        value="{{ $citizenship->document_number ?? '' }}"
                                    >
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Recent Booking History --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Recent Booking History</h5>
                    <a href="{{ route('user.booking.index') }}" class="btn btn-sm btn-outline-primary rounded-3">
                        View All
                    </a>
                </div>

                <div class="card-body px-4 pb-4">
                    @if($bookings->isEmpty())
                        <div class="text-center py-5">
                            <h6 class="fw-bold mb-2">No bookings found</h6>
                            <p class="text-muted mb-0">Your recent booking history will appear here.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th>Driver</th>
                                        <th>Pickup</th>
                                        <th>Drop</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings->take(5) as $booking)
                                        @php
                                            $hasDriver = $booking->service === 'driver';

                                            $vehicleName = $booking->vehicle?->title
                                                ?? trim(($booking->vehicle?->brand ?? '') . ' ' . ($booking->vehicle?->model ?? ''));

                                            $statusClass = match($booking->status) {
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                'confirmed' => 'bg-primary',
                                                'active' => 'bg-info text-dark',
                                                'pending' => 'bg-warning text-dark',
                                                default => 'bg-secondary',
                                            };
                                        @endphp

                                        <tr>
                                            <td>#{{ $booking->id }}</td>

                                            <td>{{ $vehicleName ?: 'N/A' }}</td>

                                            <td>
                                                <span class="badge {{ $hasDriver ? 'bg-info text-dark' : 'bg-secondary' }}">
                                                    {{ $hasDriver ? 'With Driver' : 'Self Drive' }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ $booking->service === 'driver' ? ($booking->driver->name ?? 'Driver Assigned') : 'Self Drive' }}
                                            </td>

                                            <td>
                                                {{ $booking->pickup_datetime ? \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y, h:i A') : '-' }}
                                            </td>

                                            <td>
                                                {{ $booking->drop_datetime ? \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y, h:i A') : '-' }}
                                            </td>

                                            <td>
                                                <span class="badge {{ $statusClass }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
