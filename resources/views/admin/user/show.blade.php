@extends('admin.layouts.master')

@section('title', 'User Details')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">User Details</h2>
    <p class="text-muted mb-0">View user profile, documents, and account information</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-8">

        <!-- Profile Card -->
        <div class="card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 64px; height: 64px; background: #f1e9dd; color: #4b5563; font-size: 1.5rem; font-weight: 700;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-envelope me-1"></i> {{ $user->email }}
                                </div>
                                @if($user->phone)
                                    <div class="text-muted small mt-1">
                                        <i class="fa-solid fa-phone me-1"></i> {{ $user->phone }}
                                    </div>
                                @endif
                            </div>
                            @php
                                $statusClass = match($user->status) {
                                    'approved' => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'suspended' => 'bg-secondary',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };

                                $docStatus = $user->selfDriveVerificationStatus();
                                $docStatusClass = match($docStatus) {
                                    'approved' => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'rejected', 'expired' => 'bg-danger',
                                    'missing' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <div class="d-flex flex-column align-items-end gap-2">
                                <span class="badge {{ $statusClass }} px-3 py-2">
                                    Account: {{ ucfirst($user->status ?? 'pending') }}
                                </span>
                                <span class="badge {{ $docStatusClass }} px-3 py-2">
                                    Documents: {{ ucfirst($docStatus) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-2">

                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="text-muted small">Address</div>
                        <div class="fw-semibold">{{ $user->address ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Joined</div>
                        <div class="fw-semibold">{{ $user->created_at?->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Role</div>
                        <div class="fw-semibold text-capitalize">{{ $user->role }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Quick Info & Actions -->
    <div class="col-lg-4">

        <!-- Account Stats -->
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-chart-bar me-2 text-primary"></i> Account Summary
                </h5>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted">Total Bookings</span>
                    <span class="fw-bold">{{ $user->bookings_count ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted">Loyalty Tier</span>
                    <span class="fw-bold text-capitalize">{{ $user->loyaltyAccount->tier ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted">Available Points</span>
                    <span class="fw-bold text-primary">{{ $user->loyaltyAccount->available_points ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted">Lifetime Earned</span>
                    <span class="fw-bold text-success">{{ $user->loyaltyAccount->lifetime_earned_points ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Documents Section Full Width -->
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center px-4 pt-4 mb-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-file-lines me-2 text-primary"></i> Documents
            </h5>
        </div>

        @if($user->documents->isEmpty())
            <div class="text-center text-muted py-5 px-4">
                <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                No documents uploaded by this user.
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Document No.</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->documents as $doc)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-capitalize">{{ $doc->type }}</div>
                                    <small class="text-muted">Uploaded {{ $doc->created_at?->format('d M Y') }}</small>
                                </td>
                                <td>{{ $doc->document_number ?: '—' }}</td>
                                <td>
                                    <div class="small text-muted">
                                        Issued: {{ $doc->issued_at?->format('d M Y') ?? '—' }}
                                    </div>
                                    <div class="small text-muted">
                                        Expires: {{ $doc->expires_at?->format('d M Y') ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $docStatusClass = match($doc->status) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $docStatusClass }}">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $doc->remarks ?: '—' }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                        <a href="{{ asset('storage/' . $doc->file_path) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> View
                                        </a>

                                        @if($doc->status !== 'approved')
                                            <form action="{{ route('admin.documents.approve', $doc) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fa-solid fa-check me-1"></i> Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if($doc->status !== 'rejected')
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $doc->id }}">
                                                <i class="fa-solid fa-xmark me-1"></i> Reject
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Reject Modal -->
                            @if($doc->status !== 'rejected')
                                <div class="modal fade" id="rejectModal{{ $doc->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title fw-bold">Reject {{ ucfirst($doc->type) }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.documents.reject', $doc) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    <label class="form-label">Rejection Reason</label>
                                                    <textarea name="remarks" rows="3" class="form-control" required></textarea>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Reject Document</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Booking History Section Full Width -->
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center px-4 pt-4 mb-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-receipt me-2 text-primary"></i> Booking History
            </h5>
        </div>

        @if($bookings->isEmpty())
            <div class="text-center text-muted py-5 px-4">
                <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block opacity-50"></i>
                No bookings found for this user.
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Vehicle</th>
                            <th>Trip</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Booked On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            @php
                                $bookingStatusClass = match($booking->status) {
                                    'completed' => 'bg-success',
                                    'confirmed' => 'bg-primary',
                                    'ongoing' => 'bg-info text-dark',
                                    'cancelled' => 'bg-danger',
                                    'pending' => 'bg-warning text-dark',
                                    default => 'bg-secondary',
                                };

                                $paymentStatus = $booking->payment?->status ?? $booking->payment_status ?? 'pending';
                                $paymentStatusClass = match($paymentStatus) {
                                    'completed', 'paid' => 'bg-success',
                                    'pending', 'unpaid' => 'bg-warning text-dark',
                                    'failed' => 'bg-danger',
                                    'refunded' => 'bg-info text-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $booking->vehicle?->brand }} {{ $booking->vehicle?->model }}</div>
                                    <small class="text-muted text-capitalize">{{ $booking->vehicle?->vehicle_type ?? '—' }}</small>
                                </td>
                                <td>
                                    <div class="small text-muted">{{ $booking->pickup_location ?? '—' }} → {{ $booking->drop_location ?? '—' }}</div>
                                    <small class="text-muted">{{ $booking->pickup_datetime?->format('d M Y, h:i A') ?? '—' }}</small>
                                </td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $booking->service ?? '—') }}</td>
                                <td>NPR {{ number_format((float) ($booking->total_price ?? 0), 2) }}</td>
                                <td>
                                    <span class="badge {{ $paymentStatusClass }}">{{ ucfirst($paymentStatus) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $bookingStatusClass }}">{{ ucfirst($booking->status ?? 'pending') }}</span>
                                </td>
                                <td>{{ $booking->created_at?->format('d M Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
