@extends('vendor.layouts.master')

@section('title', $driver->name . ' - Driver Details')
@section('page_title', 'Driver Details')
@section('page_subtitle', 'View details of your driver')

@section('vendor-content')
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('vendor.drivers.index') }}" class="btn btn-outline-secondary">
            ← Back to Drivers
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('vendor.drivers.edit', $driver->id) }}" class="btn btn-outline-dark">Edit Driver</a>
            <form method="POST" action="{{ route('vendor.drivers.destroy', $driver->id) }}"
                  onsubmit="return confirm('Are you sure you want to delete this driver?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Driver</button>
            </form>
        </div>
    </div>
</div>

<div class="row g-5">
    <!-- Left: Profile Card -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body text-center py-5">
                @if($driver->image)
                    <img src="{{ asset('storage/'.$driver->image) }}"
                         alt="{{ $driver->name }}"
                         class="rounded-circle mb-4 shadow-sm"
                         style="width: 160px; height: 160px; object-fit: cover; border: 6px solid #f1f5f9;">
                @else
                    <div class="mx-auto mb-4 rounded-circle bg-light d-flex align-items-center justify-content-center"
                         style="width: 160px; height: 160px; border: 6px solid #f1f5f9;">
                        <span class="text-muted fw-bold" style="font-size: 4.5rem;">
                            {{ strtoupper(substr($driver->name, 0, 1)) }}
                        </span>
                    </div>
                @endif

                <h3 class="fw-bold mb-2">{{ $driver->name }}</h3>
                <p class="text-muted mb-4">{{ $driver->phone ?? 'No phone number' }}</p>

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <span class="badge bg-success px-4 py-2">Available</span>
                    <span class="badge bg-primary px-4 py-2">Approved</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Driver Information -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Driver Information</h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="small text-muted">Full Name</div>
                        <div class="fw-semibold">{{ $driver->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Phone</div>
                        <div class="fw-semibold">{{ $driver->phone ?? '—' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="small text-muted">License Number</div>
                        <div class="fw-semibold">{{ $driver->license_number }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Rating</div>
                        <div class="fw-semibold">{{ $driver->rating ? $driver->rating . ' / 5' : 'Not Rated' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="small text-muted">Availability</div>
                        <div class="fw-semibold text-capitalize">{{ $driver->availability_status }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Status</div>
                        <div class="fw-semibold text-capitalize">{{ $driver->status }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="small text-muted">Added On</div>
                        <div class="fw-semibold">{{ $driver->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Booking Summary</h5>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Total Bookings</div>
                        <div class="fs-4 fw-bold">{{ $totalBookings ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Completed</div>
                        <div class="fs-4 fw-bold text-success">{{ $completedBookings ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-muted">Going On</div>
                        <div class="fs-4 fw-bold text-primary">{{ $ongoingBookings ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Last 3 Bookings</h6>
            @if(!empty($lastThreeBookings) && $lastThreeBookings->isNotEmpty())
                <div class="list-group list-group-flush border rounded-3 overflow-hidden mb-2">
                    @foreach($lastThreeBookings as $booking)
                        @php
                            $vehicleName = trim(($booking->vehicle->brand ?? '') . ' ' . ($booking->vehicle->model ?? ''));
                            $paymentState = $booking->payment_status ?? ($booking->payment->status ?? 'unpaid');
                        @endphp
                        <div class="list-group-item px-3 py-3">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">Booking #{{ $booking->id }}</div>
                                    <div class="text-muted small">Customer: {{ $booking->user->name ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $vehicleName ?: ($booking->vehicle->title ?? 'N/A') }}</div>
                                    <div class="text-muted small">Pickup: {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y, h:i A') }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark text-capitalize">{{ $booking->status }}</span>
                                    <div class="mt-2">
                                        <span class="badge {{ $paymentState === 'paid' ? 'bg-success' : ($paymentState === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }} text-capitalize">
                                            {{ $paymentState }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No recent bookings found for this driver.</p>
            @endif
        </div>
    </div>
</div>
@endsection
