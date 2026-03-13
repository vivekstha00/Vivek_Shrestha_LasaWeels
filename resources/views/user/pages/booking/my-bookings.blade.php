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
