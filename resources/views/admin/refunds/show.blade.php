@extends('admin.layouts.master')

@section('title', 'Refund Request Details')

@section('admin-content')
<div class="container-fluid px-0">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Refund Request Details</h2>
            <p class="text-muted mb-0">Booking #{{ $payment->booking_id ?? '—' }}</p>
        </div>

        <a href="{{ route('admin.refunds.index') }}" class="btn btn-outline-secondary btn-sm">
            Back to Refunds
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $statusClass = match($payment->refund_status) {
            'refunded' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-warning text-dark',
        };
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="small text-muted">Refund Status</div>
                    <div class="mt-1"><span class="badge {{ $statusClass }}">{{ ucfirst($payment->refund_status ?? 'pending') }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="small text-muted">Refund Amount</div>
                    <h5 class="fw-bold text-danger mb-0">NPR {{ number_format((float) ($payment->refund_amount ?? $payment->amount ?? 0), 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="small text-muted">Payment Amount</div>
                    <h5 class="fw-bold mb-0">NPR {{ number_format((float) ($payment->amount ?? 0), 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="small text-muted">Requested At</div>
                    <div class="fw-semibold small mt-1">{{ $payment->refund_requested_at?->format('d M Y, h:i A') ?? $payment->created_at?->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5">
                    <h5 class="fw-bold mb-3">Request Information</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Booking ID</div>
                            <div class="fw-semibold">#{{ $payment->booking?->id ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Booking Status</div>
                            <div class="fw-semibold text-capitalize">{{ $payment->booking?->status ?? 'N/A' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted">Reason / Note</div>
                            <div class="border rounded-3 p-3 bg-light">{{ $payment->refund_note ?? '—' }}</div>
                        </div>
                        @if($payment->refund_processed_at)
                            <div class="col-md-6">
                                <div class="small text-muted">Processed At</div>
                                <div class="fw-semibold">{{ $payment->refund_processed_at->format('d M Y, h:i A') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5">
                    <h5 class="fw-bold mb-3">User & Booking Context</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">User</div>
                            <div class="fw-semibold">{{ $payment->booking?->user?->name ?? 'N/A' }}</div>
                            <div class="small text-muted">{{ $payment->booking?->user?->email ?? '' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Vehicle</div>
                            <div class="fw-semibold">
                                {{ $payment->booking?->vehicle?->brand ?? '' }} {{ $payment->booking?->vehicle?->model ?? '' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Payment Type</div>
                            <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $payment->payment_type ?? 'N/A') }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Payment Status</div>
                            <div class="fw-semibold text-capitalize">{{ $payment->status ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Actions</h5>

            @if(($payment->refund_status ?? null) === 'pending')
                <div class="row g-3">
                    <div class="col-lg-6">
                        <form method="POST" action="{{ route('admin.refunds.approve', $payment) }}" class="h-100 border rounded-3 p-3">
                            @csrf
                            @method('PUT')

                            <label class="form-label small text-muted">Approval Note (optional)</label>
                            <textarea name="refund_note" class="form-control mb-2" rows="4" placeholder="Optional note for refund approval"></textarea>

                            <button type="submit" class="btn btn-success w-100">Approve Refund</button>
                        </form>
                    </div>

                    <div class="col-lg-6">
                        <form method="POST" action="{{ route('admin.refunds.reject', $payment) }}" class="h-100 border rounded-3 p-3">
                            @csrf
                            @method('PUT')

                            <label class="form-label small text-muted">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="refund_note" class="form-control mb-2" rows="4" placeholder="Required reason for rejection" required></textarea>

                            <button type="submit" class="btn btn-danger w-100">Reject Refund</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary mb-0">
                    This request has already been processed.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
