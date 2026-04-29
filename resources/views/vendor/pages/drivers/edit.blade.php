@extends('vendor.layouts.master')

@section('title', 'Edit Driver')
@section('page_title', 'Edit Driver')
@section('page_subtitle', 'Update driver information')

@section('vendor-content')
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Edit Driver</h2>
            <p class="text-muted">Update the details of {{ $driver->name }}</p>
        </div>
        <a href="{{ route('vendor.drivers.index') }}" class="btn btn-outline-secondary">← Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('vendor.drivers.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Driver Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $driver->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Driver Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $driver->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $driver->phone) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">License Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="license_number" value="{{ old('license_number', $driver->license_number) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Availability Status</label>
                    <select class="form-select" name="availability_status" required>
                        <option value="available" {{ $driver->availability_status == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ $driver->availability_status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Profile Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">

                    @if($driver->image)
                        <div class="mt-3">
                            <p class="small text-muted mb-2">Current Image:</p>
                            <img src="{{ asset('storage/'.$driver->image) }}"
                                 alt="Current Image"
                                 class="img-fluid rounded border"
                                 style="max-height: 180px;">
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-5">
                <button type="submit" class="btn btn-success px-5">Update Driver</button>
            </div>
        </form>
    </div>
</div>
@endsection
