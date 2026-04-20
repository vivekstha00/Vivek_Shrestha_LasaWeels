@extends('user.layouts.master')

@section('title', 'Booking Details')



@section('user-content')
<div class="container py-5">

    {{-- Flash + errors --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $payment = $booking->payment ?? null;
        $isPaid = ($booking->payment_status ?? 'unpaid') === 'paid';

        $vehicleName = $booking->vehicle->name
            ?? trim(($booking->vehicle->brand ?? '') . ' ' . ($booking->vehicle->model ?? ''))
            ?: 'N/A';

        $statusBadge = match($booking->status) {
            'confirmed' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'active' => 'bg-primary',
            'completed' => 'bg-dark',
            'cancel_requested' => 'bg-warning text-dark',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };

        $canCancel = in_array($booking->status, ['pending', 'confirmed'], true)
            && now()->lt($booking->pickup_datetime->copy()->subDay())
            && (! $payment || ($payment->refund_status ?? 'none') === 'none');

        $vehicleImage = null;
        if ($booking->vehicle?->primaryImage?->path) {
            $vehicleImage = asset('storage/' . ltrim($booking->vehicle->primaryImage->path, '/'));
        } elseif ($booking->vehicle?->images?->first()?->path) {
            $vehicleImage = asset('storage/' . ltrim($booking->vehicle->images->first()->path, '/'));
        } elseif (!empty($booking->vehicle?->image_url)) {
            $vehicleImage = asset('storage/' . ltrim($booking->vehicle->image_url, '/'));
        }

        $driverImage = !empty($booking->driver?->image)
            ? asset('storage/' . ltrim($booking->driver->image, '/'))
            : null;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Booking #{{ $booking->id }}</h3>
            <small class="text-muted">Created at: {{ optional($booking->created_at)->format('d M Y, h:i A') }}</small>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('user.booking.index') }}" class="btn btn-outline-secondary">
                ← My Bookings
            </a>

            @if(!$isPaid && !in_array($booking->status, ['cancel_requested', 'cancelled'], true))
                <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-success">
                    Pay Now
                </a>
            @endif
        </div>
    </div>

    {{-- Cancellation / refund alerts --}}
    @if($booking->status === 'cancel_requested')
        <div class="alert alert-warning rounded-3">
            <strong>Cancellation Requested:</strong>
            Your cancellation request has been submitted and is pending admin review.
            @if($payment)
                <br><strong>Refund Amount:</strong> NPR {{ number_format($payment->refund_amount ?? 0, 2) }}
            @endif
        </div>
    @endif

    @if($booking->status === 'cancelled')
        <div class="alert alert-danger rounded-3">
            <strong>Booking Cancelled.</strong>
            @if($payment && $payment->refund_status === 'refunded')
                Refund has been processed successfully.
            @endif
        </div>
    @endif

    @if($payment && $payment->refund_status === 'rejected')
        <div class="alert alert-info rounded-3">
            <strong>Refund Request Rejected:</strong>
            {{ $payment->refund_note ?? 'Your refund request was not approved.' }}
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="mb-3">Payment Summary</h5>

            <p class="mb-1"><strong>Original Price:</strong> Rs. {{ number_format($booking->original_price ?? $booking->total_price, 2) }}</p>
            <p class="mb-1"><strong>Discount:</strong> Rs. {{ number_format($booking->discount_amount ?? 0, 2) }}</p>
            <p class="mb-1"><strong>Total Price:</strong> Rs. {{ number_format($booking->total_price, 2) }}</p>

            @if(!is_null($booking->security_deposit))
                <p class="mb-1"><strong>Security Deposit:</strong> Rs. {{ number_format($booking->security_deposit, 2) }}</p>
            @endif

            <hr>

            @if($payment)
                <p class="mb-1"><strong>Method:</strong> {{ strtoupper($payment->method) }}</p>
                <p class="mb-1">
                    <strong>Payment Record:</strong>
                    <span class="badge {{ $payment->status === 'completed' ? 'bg-success' : ($payment->status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </p>

                <p class="mb-1"><strong>Paid Amount:</strong> Rs. {{ number_format($payment->paid_amount ?? 0, 2) }}</p>

                @if(($payment->refund_status ?? 'none') !== 'none')
                    <p class="mb-1">
                        <strong>Refund Status:</strong>
                        <span class="badge {{ $payment->refund_status === 'refunded' ? 'bg-success' : ($payment->refund_status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ ucfirst($payment->refund_status) }}
                        </span>
                    </p>
                    <p class="mb-1"><strong>Refund Amount:</strong> Rs. {{ number_format($payment->refund_amount ?? 0, 2) }}</p>
                @endif

                @if(!empty($payment->refund_note))
                    <p class="mb-1"><strong>Refund Note:</strong> {{ $payment->refund_note }}</p>
                @endif

                @if(!empty($payment->gateway_reference))
                    <p class="mb-0 small text-muted">Gateway Ref: {{ $payment->gateway_reference }}</p>
                @endif
            @else
                <p class="text-muted mb-0">No payment record yet.</p>
            @endif

            @if(!$isPaid && !in_array($booking->status, ['cancel_requested', 'cancelled'], true))
                <div class="mt-3">
                    <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-success">Proceed to Payment</a>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="mb-3">Booking Information</h5>

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <p class="mb-1"><strong>Vehicle:</strong> {{ $vehicleName }}</p>
                    <p class="mb-1"><strong>Service:</strong> {{ ucfirst($booking->service) }}</p>
                    <p class="mb-1">
                        <strong>Booking Status:</strong>
                        <span class="badge {{ $statusBadge }}">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </p>
                    <p class="mb-1">
                        <strong>Payment Status:</strong>
                        <span class="badge {{ $isPaid ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($booking->payment_status ?? 'unpaid') }}
                        </span>
                    </p>
                    <p class="mb-1"><strong>Pickup:</strong> {{ $booking->pickup_location }}</p>
                    <p class="mb-1"><strong>Drop:</strong> {{ $booking->drop_location }}</p>
                    <p class="mb-1">
                        <strong>Pickup Date:</strong>
                        {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y h:i A') }}
                    </p>
                    <p class="mb-1">
                        <strong>Drop Date:</strong>
                        {{ \Carbon\Carbon::parse($booking->drop_datetime)->format('d M Y h:i A') }}
                    </p>

                    @if(!empty($booking->special_request))
                        <hr>
                        <h6 class="mb-2">Special Request</h6>
                        <p class="mb-0 text-muted">{{ $booking->special_request }}</p>
                    @endif

                    @if(!empty($booking->cancellation_reason))
                        <hr>
                        <h6 class="mb-2">Cancellation Reason</h6>
                        <p class="mb-0 text-muted">{{ $booking->cancellation_reason }}</p>
                    @endif
                </div>

                <div class="col-lg-5">
                    <h6 class="mb-2">Vehicle Image</h6>
                    @if($vehicleImage)
                        <img src="{{ $vehicleImage }}" alt="Vehicle Image" class="img-fluid rounded" style="height: 220px; width: 100%; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 220px; width: 100%;">
                            <i class="fa-solid fa-car fs-1 text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($booking->status === 'completed')
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Review</h5>

                @if($booking->review)
                    @php
                        $review = $booking->review;
                    @endphp

                    <div class="alert alert-success">
                        You have already submitted a review for this booking.
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted">Overall Rating</div>
                                <div class="fw-bold">{{ number_format((float) $review->overall_rating, 1) }} ★ / 5</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted">Vehicle Rating</div>
                                <div class="fw-bold">{{ number_format((float) $review->vehicle_rating, 1) }} ★ / 5</div>
                            </div>
                        </div>
                        @if($booking->service === 'driver')
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="small text-muted">Driver Rating</div>
                                    <div class="fw-bold">{{ number_format((float) ($review->driver_rating ?? 0), 1) }} ★ / 5</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3">
                        @if(!empty($review->overall_review))
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <div class="small text-muted mb-1">Overall Review</div>
                                    <div>{{ $review->overall_review }}</div>
                                </div>
                            </div>
                        @endif

                        @if(!empty($review->vehicle_review))
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <div class="small text-muted mb-1">Vehicle Review</div>
                                    <div>{{ $review->vehicle_review }}</div>
                                </div>
                            </div>
                        @endif

                        @if($booking->service === 'driver' && !empty($review->driver_review))
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <div class="small text-muted mb-1">Driver Review</div>
                                    <div>{{ $review->driver_review }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <form action="{{ route('user.bookings.review.store', $booking) }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Overall Rating</label>
                                <select name="overall_rating" class="form-control" required>
                                    <option value="">Select Rating</option>
                                    @foreach([1,1.5,2,2.5,3,3.5,4,4.5,5] as $ratingValue)
                                        <option value="{{ $ratingValue }}">{{ $ratingValue }} Star{{ $ratingValue > 1 ? 's' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Vehicle Rating</label>
                                <select name="vehicle_rating" class="form-control" required>
                                    <option value="">Select Rating</option>
                                    @foreach([1,1.5,2,2.5,3,3.5,4,4.5,5] as $ratingValue)
                                        <option value="{{ $ratingValue }}">{{ $ratingValue }} Star{{ $ratingValue > 1 ? 's' : '' }}</option>
                                    @endforeach
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

                            @if($booking->service === 'driver')
                                <div class="col-md-6">
                                    <label class="form-label">Driver Rating</label>
                                    <select name="driver_rating" class="form-control" required>
                                        <option value="">Select Rating</option>
                                        @foreach([1,1.5,2,2.5,3,3.5,4,4.5,5] as $ratingValue)
                                            <option value="{{ $ratingValue }}">{{ $ratingValue }} Star{{ $ratingValue > 1 ? 's' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Driver Review</label>
                                    <textarea name="driver_review" rows="2" class="form-control" placeholder="Write your driver experience"></textarea>
                                </div>
                            @endif

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    @if($booking->service === 'driver')
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Driver</h5>

                <div class="row g-4 align-items-start">
                    <div class="col-lg-7">
                        @if($booking->driver)
                            <p class="mb-1"><strong>Name:</strong> {{ $booking->driver->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $booking->driver->phone ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>License:</strong> {{ $booking->driver->license_number ?? 'N/A' }}</p>
                        @else
                            <p class="mb-0 text-muted">Driver info not available.</p>
                        @endif
                    </div>

                    <div class="col-lg-5">
                        <h6 class="mb-2">Driver Image</h6>
                        @if($driverImage)
                            <img src="{{ $driverImage }}" alt="Driver Image" class="img-fluid rounded" style="height: 220px; width: 100%; object-fit: cover;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 220px; width: 100%;">
                                <i class="fa-solid fa-user fs-1 text-muted"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Cancel request card --}}
    @if($canCancel)
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="mb-3">Request Cancellation</h5>
                <p class="text-muted mb-3">
                    You can cancel this booking only if more than 24 hours remain before pickup.
                </p>

                <form action="{{ route('user.booking.cancel-request', $booking->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cancellation Reason</label>
                        <textarea
                            name="cancellation_reason"
                            class="form-control"
                            rows="3"
                            required
                        >{{ old('cancellation_reason') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-outline-danger">
                        Request Cancellation
                    </button>
                </form>
            </div>
        </div>
    @elseif(in_array($booking->status, ['pending', 'confirmed'], true) && now()->gte($booking->pickup_datetime->copy()->subDay()))
        <div class="alert alert-secondary mt-4">
            Booking cannot be cancelled within 24 hours of pickup.
        </div>
    @endif
</div>
@endsection
