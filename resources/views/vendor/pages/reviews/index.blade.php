@extends('vendor.layouts.master')

@section('title', 'Customer Reviews')
@section('page_title', 'Customer Reviews')
@section('page_subtitle', 'Reviews and ratings for your vehicles')

@section('vendor-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Customer Reviews</h2>
    <p class="text-muted mb-0">Reviews and ratings for your vehicles</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body py-4">
                <div class="text-muted small mb-2">Total Reviews</div>
                <h3 class="fw-bold mb-0">{{ $totalReviews ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body py-4">
                <div class="text-muted small mb-2">Avg Overall Rating</div>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($avgOverallRating ?? 0, 1) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body py-4">
                <div class="text-muted small mb-2">Avg Vehicle Rating</div>
                <h3 class="fw-bold text-success mb-0">{{ number_format($avgVehicleRating ?? 0, 1) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($reviews->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Booking</th>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Overall</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                            <tr style="cursor: pointer;"
                                onclick="window.location='{{ route('vendor.reviews.show', $review->id) }}'">
                                <td>#{{ $review->id }}</td>
                                <td>#{{ $review->booking_id }}</td>
                                <td>{{ $review->user?->name ?? 'N/A' }}</td>
                                <td>{{ trim(($review->vehicle?->brand ?? '') . ' ' . ($review->vehicle?->model ?? '')) ?: 'N/A' }}</td>
                                <td><span class="badge bg-primary">{{ $review->overall_rating }}/5</span></td>
                                <td><span class="badge bg-success">{{ $review->vehicle_rating }}/5</span></td>
                                <td>
                                    @if($review->driver_rating)
                                        <span class="badge bg-warning text-dark">{{ $review->driver_rating }}/5</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $review->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold text-muted">No reviews found yet.</h5>
                <p class="text-muted">Customer reviews will appear here once bookings are completed.</p>
            </div>
        @endif
    </div>
</div>
@endsection
