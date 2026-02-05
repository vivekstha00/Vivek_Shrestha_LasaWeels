@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">My Vehicles</h4>
        <a href="{{ route('vendor.vehicles.create') }}" class="btn btn-primary">
            + Add Vehicle
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Price / Day</th>
                        <th>Status</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $vehicle)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $vehicle->title }}</td>
                            <td>{{ $vehicle->vehicle_type }}</td>
                            <td>{{ $vehicle->location_city }}</td>
                            <td>{{ $vehicle->currency }} {{ number_format($vehicle->price_per_day, 2) }}</td>
                            <td>
                                <span class="badge
                                    @if($vehicle->status === 'approved') bg-success
                                    @elseif($vehicle->status === 'rejected') bg-danger
                                    @else bg-warning text-dark
                                    @endif
                                ">
                                    {{ ucfirst($vehicle->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $vehicle->is_active ? 'Yes' : 'No' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No vehicles added yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
