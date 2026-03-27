@extends('vendor.layouts.master')

@section('title', 'My Drivers')
@section('page_title', 'My Drivers')
@section('page_subtitle', 'Manage drivers and track subscription usage.')

@section('vendor-content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1">My Drivers</h2>
        <p class="text-muted mb-0">Manage drivers and track subscription usage.</p>
    </div>
    <a href="{{ route('vendor.drivers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Driver
    </a>
</div>

@if(isset($subscriptionSummary))
    <div class="card subscription-box mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>Current Plan:</strong> {{ $subscriptionSummary['plan_name'] ?? 'Basic' }}<br>
                    <strong>Driver Usage:</strong> {{ $subscriptionSummary['driver_count'] ?? 0 }} / {{ $subscriptionSummary['driver_limit'] ?? 8 }}
                </div>
                @if(!empty($subscriptionSummary['ends_at']))
                    <div class="text-muted small">
                        Expires: {{ \Carbon\Carbon::parse($subscriptionSummary['ends_at'])->format('d M Y, h:i A') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        @if($drivers->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>License</th>
                            <th>Availability</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                            <tr style="cursor: pointer;"
                                onclick="window.location='{{ route('vendor.drivers.show', $driver) }}'">
                                <td>
                                    <div class="fw-semibold">{{ $driver->name }}</div>
                                </td>
                                <td>{{ $driver->phone ?? 'N/A' }}</td>
                                <td>{{ $driver->license_number }}</td>
                                <td>
                                    <span class="badge {{ $driver->availability_status === 'available' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($driver->availability_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-capitalize">{{ $driver->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">
                {{ $drivers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold text-muted">No drivers found</h5>
                <p class="text-muted mb-3">Add your first driver to get started.</p>
                <a href="{{ route('vendor.drivers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Add Driver
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
