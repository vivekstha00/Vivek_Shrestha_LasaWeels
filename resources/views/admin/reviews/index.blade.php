@extends('admin.layouts.master')

@section('title', 'Reviews')

@section('admin-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold">Review Management</h3>
            <p class="text-muted mb-0">View all customer reviews and ratings</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <div class="text-muted small">Total Reviews</div>
                    <h3 class="fw-bold mb-0">{{ $totalReviews }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <div class="text-muted small">Avg Overall</div>
                    <h3 class="fw-bold text-primary mb-0">{{ number_format($avgOverallRating ?? 0, 1) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <div class="text-muted small">Avg Vehicle</div>
                    <h3 class="fw-bold text-success mb-0">{{ number_format($avgVehicleRating ?? 0, 1) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <div class="text-muted small">Avg Driver</div>
                    <h3 class="fw-bold text-warning mb-0">{{ number_format($avgDriverRating ?? 0, 1) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            @if($reviews->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Booking</th>
                                <th>User</th>
                                <th>Vehicle</th>
                                <th>Vendor</th>
                                <th>Driver</th>
                                <th>Overall</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $review)
                                @php
                                    $vehicleName = $review->vehicle?->title
                                        ?? trim(($review->vehicle?->brand ?? '') . ' ' . ($review->vehicle?->model ?? ''));
                                @endphp

                                <tr>
                                    <td>#{{ $review->id }}</td>
                                    <td>#{{ $review->booking_id }}</td>
                                    <td>{{ $review->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $vehicleName ?: 'N/A' }}</td>
                                    <td>{{ $review->vehicle?->vendor?->name ?? 'N/A' }}</td>
                                    <td>{{ $review->driver?->name ?? 'Self Drive' }}</td>
                                    <td><span class="badge bg-primary">{{ $review->overall_rating }}/5</span></td>
                                    <td><span class="badge bg-success">{{ $review->vehicle_rating }}/5</span></td>
                                    <td>
                                        @if($review->driver_rating)
                                            <span class="badge bg-warning text-dark">{{ $review->driver_rating }}/5</span>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $review->created_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    No reviews found.
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
