@extends('vendor.layouts.master')

@section('title', 'Drivers - Vendor')
@section('page_title', 'Drivers')
@section('page_subtitle', 'List of all drivers')

@section('vendor-content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">My Drivers</h3>
        <a href="{{ route('vendor.drivers.create') }}" class="btn btn-primary">+ Add Driver</a>
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
                            <th>Name</th>
                            <th>Phone</th>
                            <th>License No</th>
                            <th>Rating</th>
                            <th>Availability</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($drivers as $driver)
                            @php
                                $thumb = $driver->image ? asset('storage/'.$driver->image) : null;
                            @endphp

                            <tr onclick="window.location='{{ route('vendor.drivers.show', $driver->id) }}'"
                                style="cursor:pointer;" class="driver-row">
                                <td>
                                    @if($thumb)
                                        <img src="{{ $thumb }}"
                                             alt="{{ $driver->name }}"
                                             style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <div style="width:60px;height:40px;border-radius:6px;"
                                             class="bg-light d-flex align-items-center justify-content-center small text-muted">
                                            No img
                                        </div>
                                    @endif
                                </td>

                                <td>{{ $driver->name }}</td>
                                <td>{{ $driver->phone ?? '—' }}</td>
                                <td>{{ $driver->license_number }}</td>
                                <td>{{ $driver->rating ? $driver->rating . ' / 5' : '—' }}</td>
                                <td>
                                    @if($driver->availability_status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @else
                                        <span class="badge bg-secondary">Unavailable</span>
                                    @endif
                                </td>
                                <td>
                                    @if($driver->status === 'approved')
                                        <span class="badge bg-primary">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Removed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No drivers yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $drivers->links() }}
            </div>
        </div>
    </div>

</div>

{{-- Hover effect for rows --}}
<style>
    .driver-row:hover {
        background-color: #f8f9fc;
    }
</style>
@endsection
