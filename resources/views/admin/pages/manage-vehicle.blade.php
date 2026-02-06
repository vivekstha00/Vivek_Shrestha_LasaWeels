@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">Manage Vehicles</h3>

        <form method="GET" action="{{ route('admin.vehicles.index') }}" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" style="width:180px;">
                <option value="">All</option>
                <option value="pending"  {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button class="btn btn-sm btn-dark">Filter</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th style="width:260px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($vehicles as $i => $v)
                        <tr>
                            <td>{{ $vehicles->firstItem() + $i }}</td>
                            <td>
                                <div class="fw-semibold">{{ $v->vendor->name ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $v->vendor->email ?? '' }}</div>
                            </td>
                            <td>{{ $v->title }}</td>
                            <td>{{ $v->vehicle_type }}</td>
                            <td>{{ $v->location_city }}</td>
                            <td>{{ number_format($v->price_per_day, 2) }} {{ $v->currency }}</td>
                            <td>
                                @if($v->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($v->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif

                                @if($v->status === 'rejected' && $v->reject_reason)
                                    <div class="small text-muted mt-1">Reason: {{ $v->reject_reason }}</div>
                                @endif
                            </td>
                            <td>{{ $v->is_active ? 'Yes' : 'No' }}</td>
                            <td class="d-flex gap-2 flex-wrap">

                                @if($v->status === 'pending')
                                    <form method="POST" action="{{ route('admin.vehicles.approve', $v->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.vehicles.reject', $v->id) }}" class="d-flex gap-2">
                                        @csrf
                                        <input type="text" name="reject_reason" class="form-control form-control-sm"
                                               placeholder="Reject reason" required style="width:140px;">
                                        <button class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.vehicles.toggleActive', $v->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-dark">
                                            {{ $v->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No vehicles found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
