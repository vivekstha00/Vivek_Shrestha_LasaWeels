@extends('admin.layouts.master')

@section('title', 'User Management')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">User Management</h2>
    <p class="text-muted mb-0">Manage platform users and their accounts</p>
</div>

<!-- Summary Stats -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Total Users</div>
                <h3 class="fw-bold mb-0">{{ $counts['total'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Active</div>
                <h3 class="fw-bold text-success mb-0">{{ $counts['active'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Pending</div>
                <h3 class="fw-bold text-warning mb-0">{{ $counts['pending'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Suspended</div>
                <h3 class="fw-bold text-danger mb-0">{{ $counts['suspended'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-lg-6">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Search by name or email...">
            </div>
            <div class="col-lg-4">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-lg-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Contact</th>
                    <th>Join Date</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr onclick="window.location='{{ route('admin.users.show', $u->id) }}'" style="cursor:pointer;">
                        <td>
                            <div class="fw-semibold">{{ $u->name }}</div>
                            <small class="text-muted">#{{ $u->id }}</small>
                        </td>
                        <td>
                            <div class="small">{{ $u->email }}</div>
                            <div class="text-muted small">{{ $u->phone ?? '—' }}</div>
                        </td>
                        <td class="text-muted small">
                            {{ optional($u->created_at)->format('d M Y') }}
                        </td>
                        <td>
                            @php
                                $statusClass = match($u->status) {
                                    'approved' => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'suspended' => 'bg-secondary',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };

                                $docStatus = $u->selfDriveVerificationStatus();
                                $docStatusClass = match($docStatus) {
                                    'approved' => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'rejected', 'expired' => 'bg-danger',
                                    'missing' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($u->status ?? 'pending') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $docStatusClass }}">
                                {{ ucfirst($docStatus) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                @if($u->status === 'pending')
                                    <form method="POST" action="{{ route('admin.users.approve', $u->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.reject', $u->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger ms-1">Reject</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                                        View
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top">
        {{ $users->links() }}
    </div>
</div>
@endsection
