@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <div class="d-flex align-items-end justify-content-between mb-3">
        <div>
            <h2 class="fw-bold mb-1">Vehicles</h2>
        </div>

        <form method="GET" action="{{ route('admin.vehicles.index') }}" class="d-flex gap-2">
            <select name="status" class="form-select" style="width:180px;">
                <option value="">All</option>
                <option value="pending"  {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button class="btn btn-dark">Filter</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Vendor</th>
                        <th>Vehicle</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Price/Day</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th style="width:340px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($vehicles as $i => $v)
                    @php $status = $v->status ?? 'pending'; @endphp
                    <tr>
                        <td>{{ $vehicles->firstItem() + $i }}</td>

                        <td>
                            <div class="fw-semibold">{{ $v->vendor->name ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $v->vendor->email ?? '' }}</div>
                        </td>

                        <td class="fw-semibold">{{ $v->title }}</td>
                        <td class="text-muted">{{ $v->vehicle_type }}</td>
                        <td class="text-muted">{{ $v->location_city }}</td>
                        <td class="fw-semibold">{{ number_format($v->price_per_day, 2) }} {{ $v->currency }}</td>

                        <td>
                            @if($status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                            @endif

                            @if($status === 'rejected' && !empty($v->reject_reason))
                                <div class="text-muted small mt-1">
                                    Reason: {{ $v->reject_reason }}
                                </div>
                            @endif
                        </td>

                        <td class="text-muted">{{ $v->is_active ? 'Yes' : 'No' }}</td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($status === 'pending')
                                    <form method="POST" action="{{ route('admin.vehicles.approve', $v->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.vehicles.reject', $v->id) }}" class="d-flex gap-2">
                                        @csrf
                                        <input type="text" name="reject_reason" class="form-control form-control-sm"
                                               placeholder="Reject reason" required style="width:150px;">
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
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No vehicles found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $vehicles->links() }}
        </div>
    </div>

</div>
@endsection
