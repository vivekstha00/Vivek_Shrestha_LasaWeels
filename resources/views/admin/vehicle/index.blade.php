@extends('admin.layouts.master')

@section('title', 'Vehicles')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Vehicles</h2>
    <p class="text-muted">Manage all vehicles on the platform</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Vendor</th>
                    <th>Vehicle</th>
                    <th>Type</th>
                    <th>Price/Day</th>
                    <th>Status</th>
                    <th>Active</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                    <tr onclick="window.location='{{ route('admin.vehicles.show', $vehicle) }}'" style="cursor:pointer;">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $vehicle->vendor?->company_name ?? $vehicle->vendor?->name ?? '—' }}</td>
                        <td class="fw-semibold">{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                        <td class="text-capitalize">{{ $vehicle->vehicle_type }}</td>
                        <td>NPR {{ number_format($vehicle->price_per_day, 2) }}</td>
                        <td>
                            <span class="badge {{ $vehicle->status === 'approved' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </td>
                        <td>{{ $vehicle->is_active ? 'Yes' : 'No' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                @if($vehicle->status === 'pending')
                                    <form method="POST" action="{{ route('admin.vehicles.approve', $vehicle) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.vehicles.reject', $vehicle) }}">
                                        @csrf
                                        <input type="hidden" name="reject_reason" value="Rejected by admin from list view">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.vehicles.toggleActive', $vehicle) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        {{ $vehicle->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No vehicles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $vehicles->links() }}
    </div>
</div>
@endsection
