@extends('vendor.layouts.master')

@section('title', 'Bookings')

@section('vendor-content')
<div class="container-fluid">

    <h4 class="mb-4">Bookings</h4>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered align-middle">
                <thead class="table-light">
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
                        <tr style="cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor=''" onclick="window.location='{{ route('vendor.bookings.show', $booking->id) }}'">
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->vehicle?->title ?? 'N/A' }}</td>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y') }}</td>

                            <td>
                                <span class="badge bg-warning">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge
                                    {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>

                            <td>Rs. {{ number_format($booking->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $bookings->links() }}

        </div>
    </div>

</div>
@endsection
