@extends('admin.layouts.master')

@section('admin-content')
<div class="mb-3">
    <h2 class="fw-bold mb-1">Platform Overview</h2>
    <p class="text-muted mb-0">Monitor and manage the entire platform</p>
</div>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total Users</small>
                <h3 class="fw-bold mb-0">{{ $statistics['totalUsers'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total Vendors</small>
                <h3 class="fw-bold mb-0">{{ $statistics['totalVendors'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total Vehicles</small>
                <h3 class="fw-bold mb-0">{{ $statistics['totalVehicles'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Pending Approvals</small>
                <h3 class="fw-bold mb-0">
                    {{ ($statistics['pendingVendors'] ?? 0) + ($statistics['pendingVehicles'] ?? 0) + ($statistics['pendingDocs'] ?? 0) }}
                </h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-bold bg-white">Pending Approvals</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="border rounded p-3">
                    <div class="text-muted small">Vendors</div>
                    <div class="fw-bold fs-4">{{ $statistics['pendingVendors'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3">
                    <div class="text-muted small">Vehicles</div>
                    <div class="fw-bold fs-4">{{ $statistics['pendingVehicles'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3">
                    <div class="text-muted small">Documents</div>
                    <div class="fw-bold fs-4">{{ $statistics['pendingDocs'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-bold bg-white">Recent Users</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($statistics['recentUsers'] ?? []) as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td class="text-muted">{{ $user->email }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted py-3">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
