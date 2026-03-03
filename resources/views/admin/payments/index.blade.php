@extends('admin.layouts.master')

@section('title', 'Payments')

@section('admin-content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Payments</h3>
    </div>

    <form class="row g-2 mb-3" method="GET" action="{{ route('admin.payments.index') }}">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by payment id, booking id, user name/email">
        </div>

        <div class="col-md-3">
            <select name="method" class="form-select">
                <option value="">All Methods</option>
                <option value="cash" {{ request('method')=='cash'?'selected':'' }}>Cash</option>
                <option value="khalti" {{ request('method')=='khalti'?'selected':'' }}>Khalti</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                <option value="failed" {{ request('status')=='failed'?'selected':'' }}>Failed</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Booking</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td>#{{ $p->id }}</td>
                        <td>
                            {{ $p->user->name ?? 'N/A' }} <br>
                            <small class="text-muted">{{ $p->user->email ?? '' }}</small>
                        </td>
                        <td>#{{ $p->booking_id }}</td>
                        <td>{{ strtoupper($p->method) }}</td>
                        <td>
                            <span class="badge
                                {{ $p->status==='completed' ? 'bg-success' : ($p->status==='failed' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td>Rs. {{ number_format($p->amount, 2) }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.payments.show', $p->id) }}" class="btn btn-sm btn-outline-secondary">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No payments found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>

</div>
@endsection
