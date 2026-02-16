@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <div class="mb-3">
        <h2 class="fw-bold mb-1">User Management</h2>
        <p class="text-muted mb-0">Manage platform users and their accounts</p>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Total Users</small>
                    <h3 class="fw-bold mb-0">{{ $counts['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Active</small>
                    <h3 class="fw-bold mb-0">{{ $counts['active'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Pending</small>
                    <h3 class="fw-bold mb-0">{{ $counts['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Suspended</small>
                    <h3 class="fw-bold mb-0">{{ $counts['suspended'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Search by name or email...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="approved"  {{ request('status')=='approved' ? 'selected' : '' }}>Active</option>
                        <option value="pending"   {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="suspended" {{ request('status')=='suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="rejected"  {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-dark">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th style="width:220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    @php $status = $u->status ?? 'pending'; @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $u->name }}</div>
                            <div class="text-muted small">#{{ $u->id }}</div>
                        </td>

                        <td>
                            <div class="text-muted small">{{ $u->email }}</div>
                            <div class="text-muted small">{{ $u->phone ?? '-' }}</div>
                        </td>

                        <td class="text-muted">{{ $u->created_at?->format('M d, Y') }}</td>

                        <td>
                            @if($status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @elseif($status === 'suspended')
                                <span class="badge bg-secondary">Suspended</span>
                            @else
                                <span class="badge bg-light text-dark">{{ ucfirst($status) }}</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($status === 'pending')
                                    <form method="POST" action="{{ route('admin.users.approve', $u->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.reject', $u->id) }}" class="d-flex gap-2">
                                        @csrf
                                        <input name="note" class="form-control form-control-sm"
                                               placeholder="Reject note" style="width:120px;">
                                        <button class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @else
                                    <span class="text-muted small">No action</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
