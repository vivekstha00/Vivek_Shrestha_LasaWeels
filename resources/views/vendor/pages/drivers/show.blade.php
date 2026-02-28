@extends('vendor.layouts.master')

@section('title', $driver->name . ' - Driver Details')
@section('page_title', 'Driver Details')
@section('page_subtitle', 'View details of your driver')

@section('vendor-content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('vendor.drivers.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Back to Drivers
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('vendor.drivers.edit', $driver->id) }}" class="btn btn-outline-dark btn-sm">Edit</a>
            <form method="POST" action="{{ route('vendor.drivers.destroy', $driver->id) }}"
                  onsubmit="return confirm('Delete this driver?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <div class="row">
                {{-- Left: Image + Name --}}
                <div class="col-md-3 text-center border-end">
                    @if($driver->image)
                        <img src="{{ asset('storage/'.$driver->image) }}"
                             alt="{{ $driver->name }}"
                             class="rounded-circle mb-3"
                             style="width:120px;height:120px;object-fit:cover;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width:120px;height:120px;">
                            <span class="text-muted fs-2 fw-bold">{{ strtoupper(substr($driver->name, 0, 1)) }}</span>
                        </div>
                    @endif

                    <h5 class="fw-bold mb-1">{{ $driver->name }}</h5>
                    <p class="text-muted small mb-2">{{ $driver->phone ?? 'No phone' }}</p>

                    <div class="d-flex justify-content-center gap-2">
                        @if($driver->availability_status === 'available')
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-secondary">Unavailable</span>
                        @endif

                        @if($driver->status === 'approved')
                            <span class="badge bg-primary">Approved</span>
                        @else
                            <span class="badge bg-danger">Removed</span>
                        @endif
                    </div>
                </div>

                {{-- Right: Details --}}
                <div class="col-md-9 ps-md-4 mt-4 mt-md-0">
                    <h5 class="fw-bold mb-3">Driver Information</h5>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small">Full Name</div>
                            <div class="fw-semibold">{{ $driver->name }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small">Phone</div>
                            <div class="fw-semibold">{{ $driver->phone ?? '—' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small">License Number</div>
                            <div class="fw-semibold">{{ $driver->license_number }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small">Rating</div>
                            <div class="fw-semibold">{{ $driver->rating ? $driver->rating . ' / 5' : '—' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small">Availability</div>
                            <div class="fw-semibold">{{ ucfirst($driver->availability_status) }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small">Status</div>
                            <div class="fw-semibold">{{ ucfirst($driver->status) }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small">Added On</div>
                            <div class="fw-semibold">{{ $driver->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
