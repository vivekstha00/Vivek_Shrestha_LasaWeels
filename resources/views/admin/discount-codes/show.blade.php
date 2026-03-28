@extends('admin.layouts.master')

@section('title', 'Discount Code Details')

@section('admin-content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Discount Code Details</h3>
            <p class="text-muted mb-0">View and manage this campaign</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            <a href="{{ route('admin.discount-codes.edit', $discountCode) }}" class="btn btn-primary btn-sm">Edit</a>
            <form method="POST"
                  action="{{ route('admin.discount-codes.destroy', $discountCode) }}"
                  class="d-inline"
                  onsubmit="return confirm('Delete this discount code?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 p-lg-5" style="min-height: 340px;">
            <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1">{{ $discountCode->title }}</h4>
                    @if($discountCode->description)
                        <p class="text-muted mb-0">{{ $discountCode->description }}</p>
                    @endif
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <span class="badge {{ $discountCode->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $discountCode->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="badge bg-dark">{{ $discountCode->code }}</span>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="small text-muted">Type</div>
                    <div class="fw-semibold text-capitalize">{{ $discountCode->type }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="small text-muted">Discount Value</div>
                    <div class="fw-semibold text-primary">
                        @if($discountCode->type === 'percentage')
                            {{ $discountCode->value }}%
                        @else
                            NPR {{ number_format((float) $discountCode->value, 2) }}
                        @endif
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="small text-muted">Max Discount</div>
                    <div class="fw-semibold">
                        {{ $discountCode->max_discount_amount ? 'NPR ' . number_format((float) $discountCode->max_discount_amount, 2) : 'No cap' }}
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="small text-muted">Usage Limit</div>
                    <div class="fw-semibold">{{ $discountCode->usage_limit ?? 'Unlimited' }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="small text-muted">Valid From</div>
                    <div class="fw-semibold">{{ $discountCode->valid_from?->format('d M Y, h:i A') ?? 'Not set' }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="small text-muted">Valid Until</div>
                    <div class="fw-semibold">{{ $discountCode->valid_until?->format('d M Y, h:i A') ?? 'Not set' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="small text-muted">Created</div>
                    <div class="fw-semibold">{{ $discountCode->created_at?->format('d M Y, h:i A') }}</div>
                </div>

                <div class="col-md-6">
                    <div class="small text-muted">Tracked Usage</div>
                    <div class="fw-semibold">{{ $discountCode->used_count }}</div>
                </div>
            </div>
        </div>
    </div>

    @php
        $notCompletedUsageBookings = max(0, $totalUsageBookings - $completedUsageBookings);
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="small text-muted">Total Uses</div>
                    <h4 class="fw-bold mb-0">{{ $totalUsageBookings }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="small text-muted">Completed / Applied</div>
                    <h4 class="fw-bold text-success mb-0">{{ $completedUsageBookings }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="small text-muted">Not Completed</div>
                    <h4 class="fw-bold text-warning mb-0">{{ $notCompletedUsageBookings }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h5 class="fw-bold mb-0">Users Who Used / Are Using This Code</h5>
            </div>

            @if($usageBookings->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Booking</th>
                                <th>User</th>
                                <th>Vehicle</th>
                                <th>Discount</th>
                                <th>Total Paid</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usageBookings as $booking)
                                @php
                                    $vehicleName = trim(($booking->vehicle?->brand ?? '') . ' ' . ($booking->vehicle?->model ?? ''));
                                    $usageState = match(true) {
                                        in_array($booking->payment_status, ['paid', 'partial'], true) => ['label' => 'Applied', 'class' => 'text-bg-success'],
                                        in_array($booking->status, ['pending', 'confirmed', 'active'], true) => ['label' => 'In Progress', 'class' => 'text-bg-warning'],
                                        default => ['label' => ucfirst($booking->status ?? 'Unknown'), 'class' => 'text-bg-secondary'],
                                    };

                                    $paidAmount = $booking->payment?->paid_amount ?? $booking->total_price;
                                @endphp

                                <tr>
                                    <td>#{{ $booking->id }}</td>
                                    <td>{{ $booking->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $vehicleName ?: ($booking->vehicle?->title ?? 'N/A') }}</td>
                                    <td>
                                        @if($booking->discount_amount)
                                            NPR {{ number_format((float) $booking->discount_amount, 2) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>NPR {{ number_format((float) ($paidAmount ?? 0), 2) }}</td>
                                    <td>
                                        <span class="badge {{ $usageState['class'] }}">{{ $usageState['label'] }}</span>
                                    </td>
                                    <td>{{ $booking->created_at?->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $usageBookings->links() }}
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    No user has used this discount code yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
