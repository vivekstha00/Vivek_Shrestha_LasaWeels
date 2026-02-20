@extends('vendor.layouts.master')

@section('title', 'Vehicles - Vendor')
@section('page_title', 'Vehicles')
@section('page_subtitle', 'List of all vehicles')

@section('vendor-content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">My Vehicles</h3>
        <a href="{{ route('vendor.vehicles.create') }}" class="btn btn-primary">+ Add Vehicle</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px;">Image</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Registration No</th>
                            <th>Fuel Type</th>
                            <th>Transmission</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($vehicles as $v)
                            @php
                                $thumb = $v->image_url
                                    ? asset('storage/'.$v->image_url)
                                    : ($v->images->first() ? asset('storage/'.$v->images->first()->path) : null);
                            @endphp

                            <tr onclick="window.location='{{ route('vendor.vehicles.show', $v->id) }}'"
                                style="cursor:pointer;" class="vehicle-row">
                                <td>
                                    @if($thumb)
                                        <img src="{{ $thumb }}"
                                             alt="Vehicle"
                                             style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <div style="width:60px;height:40px;border-radius:6px;"
                                             class="bg-light d-flex align-items-center justify-content-center small text-muted">
                                            No img
                                        </div>
                                    @endif
                                </td>

                                <td>{{ $v->vehicle_type }}</td>
                                <td>{{ $v->model }}</td>
                                <td>{{ $v->registration_no }}</td>
                                <td>{{ $v->fuel_type }}</td>
                                <td>{{ $v->transmission }}</td>
                                <td>
                                    @switch($v->status)
                                        @case('available')
                                            <span class="badge bg-primary">Available</span>
                                            @break
                                        @case('rented')
                                            <span class="badge bg-info text-dark">Rented</span>
                                            @break
                                        @case('maintenance')
                                            <span class="badge bg-secondary">Maintenance</span>
                                            @break
                                        @case('inactive')
                                            <span class="badge bg-dark">Inactive</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">{{ ucfirst($v->status) }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No vehicles yet.</td>
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

{{-- Hover effect for rows --}}
<style>
    .vehicle-row:hover {
        background-color: #f8f9fc;
    }
</style>
@endsection
