@extends('admin.layouts.master')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="fw-bold mb-1">Refund Requests</h2>
        <p class="text-muted mb-0">Review and process booking cancellation refunds</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if($refundPayments->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Paid Amount</th>
                            <th>Refund Amount</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($refundPayments as $payment)
                            @php
                                $booking = $payment->booking;
                            @endphp
                            <tr>
                                <td>#{{ $booking->id ?? 'N/A' }}</td>
                                <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                <td>
                                    {{ $booking->vehicle->brand ?? '' }}
                                    {{ $booking->vehicle->model ?? '' }}
                                </td>
                                <td>NPR {{ number_format($payment->paid_amount, 2) }}</td>
                                <td>NPR {{ number_format($payment->refund_amount, 2) }}</td>
                                <td>
                                    @if($payment->refund_status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($payment->refund_status === 'refunded')
                                        <span class="badge bg-success">Refunded</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $booking->cancellation_reason ?? '-' }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    @if($payment->refund_status === 'pending')
                                        <div class="d-flex flex-column gap-2">
                                            <form action="{{ route('admin.refunds.approve', $payment) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="refund_note" class="form-control form-control-sm mb-2" placeholder="Approve note (optional)">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 w-100">
                                                    Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.refunds.reject', $payment) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="refund_note" class="form-control form-control-sm mb-2" placeholder="Reject reason" required>
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 w-100">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <small class="text-muted">
                                            {{ $payment->refund_note ?? '-' }}
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $refundPayments->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No refund requests found</h5>
                <p class="text-muted mb-0">Refund requests will appear here after users cancel eligible bookings.</p>
            </div>
        @endif
    </div>
</div>
@endsection
