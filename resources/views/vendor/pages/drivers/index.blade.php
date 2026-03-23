@extends('vendor.layouts.master')

@section('vendor-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">My Drivers</h2>
        <p class="text-muted mb-0">Manage drivers and track subscription usage.</p>
    </div>
    <a href="{{ route('vendor.drivers.create') }}" class="btn btn-primary rounded-pill px-4">
        + Add Driver
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
@endif

@if(isset($subscriptionSummary))
    <div class="alert alert-info rounded-3 mb-4">
        <strong>Current Plan:</strong> {{ $subscriptionSummary['plan_name'] ?? 'Free Plan' }}<br>
        <strong>Driver Usage:</strong> {{ $subscriptionSummary['driver_count'] ?? 0 }} / {{ $subscriptionSummary['driver_limit'] ?? 2 }}

        @if(!empty($subscriptionSummary['ends_at']))
            <br><strong>Expires On:</strong> {{ \Carbon\Carbon::parse($subscriptionSummary['ends_at'])->format('Y-m-d h:i A') }}
        @endif
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if($drivers->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>License</th>
                            <th>Availability</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $driver->name }}</div>
                                    @if($driver->rating)
                                        <small class="text-muted">Rating: {{ $driver->rating }}/5</small>
                                    @endif
                                </td>
                                <td>{{ $driver->phone ?? 'N/A' }}</td>
                                <td>{{ $driver->license_number }}</td>
                                <td>
                                    <span class="badge {{ $driver->availability_status === 'available' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($driver->availability_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-capitalize">{{ $driver->status }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('vendor.drivers.show', $driver) }}"
                                           class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                            View
                                        </a>
                                        <a href="{{ route('vendor.drivers.edit', $driver) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $drivers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No drivers found</h5>
                <p class="text-muted mb-3">Start by adding your first driver.</p>
                <a href="{{ route('vendor.drivers.create') }}" class="btn btn-primary rounded-pill px-4">
                    Add Driver
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
