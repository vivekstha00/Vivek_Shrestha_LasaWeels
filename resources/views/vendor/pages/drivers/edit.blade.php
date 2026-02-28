@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container-fluid py-4">
    <h3>Edit Driver</h3>

    <form action="{{ route('vendor.drivers.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Driver Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $driver->name) }}" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $driver->phone) }}" required>
        </div>

        <div class="form-group">
            <label for="license_number">License Number</label>
            <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number', $driver->license_number) }}" required>
        </div>

        <div class="form-group">
            <label for="availability_status">Availability Status</label>
            <select class="form-control" name="availability_status" required>
                <option value="available" {{ $driver->availability_status == 'available' ? 'selected' : '' }}>Available</option>
                <option value="unavailable" {{ $driver->availability_status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>
        </div>

        <div class="form-group">
            <label for="rating">Rating (Optional)</label>
            <input type="number" class="form-control" id="rating" name="rating" min="0" max="5" value="{{ old('rating', $driver->rating) }}">
        </div>

        <div class="form-group">
            <label for="image">Profile Image</label>
            <input type="file" class="form-control" id="image" name="image">
            @if($driver->image)
                <img src="{{ asset('storage/'.$driver->image) }}" alt="Driver Image" class="img-fluid mt-2" width="150">
            @endif
        </div>

        <button type="submit" class="btn btn-success mt-3">Update Driver</button>
    </form>
</div>
@endsection
