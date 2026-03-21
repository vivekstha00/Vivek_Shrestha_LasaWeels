@extends('user.layouts.master')

@section('title', 'Booking History')

@section('user-content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Booking History</h3>
            <p class="text-muted mb-0">View all your vehicle bookings and payment details</p>
        </div>
        <a href="{{ route('user.profile') }}" class="btn btn-outline-primary btn-sm">Back to Profile</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('user.booking.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Pickup Date (From)</label>
                        <input type="date" name="from_date" class="form-control rounded-3" value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Drop Date (To)</label>
                        <input type="date" name="to_date" class="form-control rounded-3" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select rounded-3">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Service</label>
                        <select name="service" class="form-select rounded-3">
                            <option value="">All Service</option>
                            <option value="self" {{ request('service') == 'self' ? 'selected' : '' }}>Self Drive</option>
                            <option value="driver" {{ request('service') == 'driver' ? 'selected' : '' }}>With Driver</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-3">
                            Filter
                        </button>

                        <a href="{{ route('user.booking.index') }}" class="btn btn-outline-secondary w-100 rounded-3">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($bookings->count() === 0)
        <div class="alert alert-info">You haven’t made any bookings yet.</div>
    @else
        <div class="card shadow-sm border-0 rounded-4">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Vehicle</th>
                            <th>Type</th>
                            <th>Driver</th>
                            <th>Pickup</th>
                            <th>Drop</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Review</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $b)
                            @php
                                $vehicleName = $b->vehicle?->title
                                    ?? trim(($b->vehicle?->brand ?? '') . ' ' . ($b->vehicle?->model ?? ''));
                                $vehicleName = $vehicleName ?: 'N/A';

                                $hasDriver = $b->service === 'driver';

                                $statusClass = match($b->status) {
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    'confirmed' => 'bg-primary',
                                    'active' => 'bg-info text-dark',
                                    'pending' => 'bg-warning text-dark',
                                    default => 'bg-secondary',
                                };

                                $paymentClass = match($b->payment_status ?? 'unpaid') {
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-warning text-dark',
                                    'unpaid' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp

                            <tr>
                                <td>#{{ $b->id }}</td>

                                <td>{{ $vehicleName }}</td>

                                <td>
                                    <span class="badge {{ $hasDriver ? 'bg-info text-dark' : 'bg-secondary' }}">
                                        {{ $hasDriver ? 'With Driver' : 'Self Drive' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $hasDriver ? ($b->driver->name ?? 'Driver Assigned') : 'Self Drive' }}
                                </td>

                                <td>
                                    {{ $b->pickup_datetime ? \Carbon\Carbon::parse($b->pickup_datetime)->format('d M Y, h:i A') : '-' }}
                                </td>

                                <td>
                                    {{ $b->drop_datetime ? \Carbon\Carbon::parse($b->drop_datetime)->format('d M Y, h:i A') : '-' }}
                                </td>

                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($b->status) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge {{ $paymentClass }}">
                                        {{ ucfirst($b->payment_status ?? 'unpaid') }}
                                    </span>

                                    @if($b->payment)
                                        <div class="small text-muted mt-1">
                                            {{ strtoupper($b->payment->method) }}
                                        </div>
                                    @endif
                                </td>

                                <td>Rs. {{ number_format($b->total_price, 2) }}</td>
                                <td>
                                    @if($b->status === 'completed')
                                        @if($b->review)
                                            <span class="badge bg-success">Reviewed</span>
                                        @else
                                            <button
                                                class="btn btn-sm btn-primary"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#reviewForm{{ $b->id }}"
                                                aria-expanded="false"
                                            >
                                                Write Review
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted small">Not available</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('user.booking.show', $b->id) }}" class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>

                                    @if(($b->payment_status ?? 'unpaid') !== 'paid')
                                        <a href="{{ route('booking.payment', $b->id) }}" class="btn btn-sm btn-success">
                                            Pay Now
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @if($b->status === 'completed' && !$b->review)
                                <tr class="collapse" id="reviewForm{{ $b->id }}">
                                    <td colspan="11">
                                        <div class="p-3 bg-light border-top">
                                            <h6 class="fw-bold mb-3">Submit Review for Booking #{{ $b->id }}</h6>

                                            <form action="{{ route('user.bookings.review.store', $b) }}" method="POST">
                                                @csrf

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Overall Rating</label>
                                                        <select name="overall_rating" class="form-control" required>
                                                            <option value="">Select Rating</option>
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <option value="{{ $i }}">{{ $i }} Star</option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Vehicle Rating</label>
                                                        <select name="vehicle_rating" class="form-control" required>
                                                            <option value="">Select Rating</option>
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <option value="{{ $i }}">{{ $i }} Star</option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label">Overall Review</label>
                                                        <textarea name="overall_review" rows="2" class="form-control" placeholder="Write your overall trip experience"></textarea>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label">Vehicle Review</label>
                                                        <textarea name="vehicle_review" rows="2" class="form-control" placeholder="Write your vehicle experience"></textarea>
                                                    </div>

                                                    @if($hasDriver)
                                                        <div class="col-md-6">
                                                            <label class="form-label">Driver Rating</label>
                                                            <select name="driver_rating" class="form-control" required>
                                                                <option value="">Select Rating</option>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}">{{ $i }} Star</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <label class="form-label">Driver Review</label>
                                                            <textarea name="driver_review" rows="2" class="form-control" placeholder="Write your driver experience"></textarea>
                                                        </div>
                                                    @endif

                                                    <div class="col-12 text-end">
                                                        <button type="submit" class="btn btn-success">
                                                            Submit Review
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
