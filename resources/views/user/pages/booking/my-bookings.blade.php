@extends('user.layouts.master')

@section('title', 'My Bookings')

@section('user-content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">My Bookings</h3>
        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">Back to Home</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($bookings->count() === 0)
        <div class="alert alert-info">You haven’t made any bookings yet.</div>
    @else
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Vehicle</th>
                            <th>Service</th>
                            <th>Booking Status</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $b)
                            @php
                                $vehicleName = $b->vehicle->name
                                    ?? (($b->vehicle->brand ?? '').' '.($b->vehicle->model ?? ''))
                                    ?? 'N/A';
                            @endphp
                            <tr>
                                <td>#{{ $b->id }}</td>
                                <td>{{ $vehicleName }}</td>
                                <td>{{ ucfirst($b->service) }}</td>
                                <td>
                                    <span class="badge {{ $b->status === 'confirmed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($b->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ ($b->payment_status ?? 'unpaid') === 'paid' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($b->payment_status ?? 'unpaid') }}
                                    </span>
                                    @if($b->payment)
                                        <div class="small text-muted">{{ strtoupper($b->payment->method) }}</div>
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
