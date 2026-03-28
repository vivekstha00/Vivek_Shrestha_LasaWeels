@extends('admin.layouts.master')

@section('title', 'Refund Requests')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Refund Requests</h2>
    <p class="text-muted">Review and process booking cancellation refunds</p>
</div>

<div class="card">
    <div class="card-body">
        @if($refundPayments->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Booking ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Requested At</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($refundPayments as $refund)
                            <tr class="cursor-pointer"
                                role="button"
                                tabindex="0"
                                onclick="window.location='{{ route('admin.refunds.show', $refund) }}'"
                                onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location='{{ route('admin.refunds.show', $refund) }}'; }">
                                <td>#{{ $refund->booking_id ?? $refund->booking?->id ?? '—' }}</td>
                                <td>{{ $refund->booking?->user?->name ?? 'N/A' }}</td>
                                <td>NPR {{ number_format((float) ($refund->amount ?? 0), 2) }}</td>
                                <td>{{ $refund->refund_note ?? '—' }}</td>
                                <td>{{ $refund->refund_requested_at?->format('d M Y, h:i A') ?? $refund->created_at?->format('d M Y, h:i A') }}</td>
                                <td>
                                    @php
                                        $statusClass = match($refund->refund_status) {
                                            'refunded' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($refund->refund_status ?? 'pending') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.refunds.show', $refund) }}"
                                       class="btn btn-sm {{ ($refund->refund_status ?? null) === 'pending' ? 'btn-outline-primary' : 'btn-outline-secondary' }}">
                                        {{ ($refund->refund_status ?? null) === 'pending' ? 'Review' : 'Details' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $refundPayments->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No refund requests found</h5>
                <p class="text-muted">Refund requests will appear here after users cancel eligible bookings.</p>
            </div>
        @endif
    </div>
</div>
@endsection
