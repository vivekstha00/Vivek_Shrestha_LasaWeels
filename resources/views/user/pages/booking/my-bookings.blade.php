@extends('user.layouts.master')

@section('title', 'Booking History')

@section('user-content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">My Bookings</h2>
                    <p class="text-muted mb-0">View and manage your vehicle bookings</p>
                </div>
                <a href="{{ route('user.profile') }}" class="btn btn-outline-primary">Back to Profile</a>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Filter Section --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filter Bookings</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('user.booking.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Service</label>
                                <select name="service" class="form-select">
                                    <option value="">All Service</option>
                                    <option value="self" {{ request('service') == 'self' ? 'selected' : '' }}>Self Drive</option>
                                    <option value="driver" {{ request('service') == 'driver' ? 'selected' : '' }}>With Driver</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('user.booking.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bookings Table --}}
            @if($bookings->count() === 0)
                <div class="alert alert-info text-center">
                    <h5>No Bookings Found</h5>
                    <p>You haven't made any bookings yet.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">Browse Vehicles</a>
                </div>
            @else
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Your Bookings ({{ $bookings->total() }})</h5>
                        <small class="text-muted">Page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }}</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Vehicle</th>
                                    <th>Service</th>
                                    <th>Pickup Date</th>
                                    <th>Drop Date</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Amount</th>
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

                                    <tr style="cursor: pointer;" onclick="window.location='{{ route('user.booking.show', $b->id) }}'">
                                        <td>
                                            <strong>#{{ $b->id }}</strong><br>
                                            <small class="text-muted">{{ $b->pickup_location }}</small>
                                        </td>

                                        <td>
                                            <strong>{{ $vehicleName }}</strong>
                                            @if($hasDriver && $b->driver)
                                                <br><small class="text-muted">Driver: {{ $b->driver->name }}</small>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge {{ $hasDriver ? 'bg-info text-dark' : 'bg-secondary' }}">
                                                {{ $hasDriver ? 'With Driver' : 'Self Drive' }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $b->pickup_datetime ? \Carbon\Carbon::parse($b->pickup_datetime)->format('M d, Y h:i A') : '-' }}
                                        </td>

                                        <td>
                                            {{ $b->drop_datetime ? \Carbon\Carbon::parse($b->drop_datetime)->format('M d, Y h:i A') : '-' }}
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
                                                <br><small class="text-muted">{{ strtoupper($b->payment->method) }}</small>
                                            @endif
                                        </td>

                                        <td>
                                            <strong class="text-success">Rs. {{ number_format($b->total_price, 2) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($bookings->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-center">
                                {{ $bookings->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
