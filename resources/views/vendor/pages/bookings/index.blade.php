@extends('vendor.layouts.master')

@section('title', 'Bookings')
@section('page_title', 'Bookings')
@section('page_subtitle', 'Manage all your vehicle bookings')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Bookings</h2>
    <p class="text-muted mb-0">View and manage customer bookings</p>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vehicle</th>
                        <th>User</th>
                        <th>Pickup</th>
                        <th>Drop</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr style="cursor: pointer;"
                            onclick="window.location='{{ route('vendor.bookings.show', $booking->id) }}'">
                            <td><strong>#{{ $booking->id }}</strong></td>
                            <td>{{ $booking->vehicle?->title ?? $booking->vehicle?->brand ?? 'N/A' }}</td>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y') }}</td>
                            <td>
                                <span class="badge
                                    {{ $booking->status === 'pending' ? 'bg-warning' :
                                       ($booking->status === 'confirmed' ? 'bg-primary' : 'bg-success') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge
                                    {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td class="fw-medium">Rs. {{ number_format($booking->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No bookings found yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
