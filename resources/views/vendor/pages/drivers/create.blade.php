@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container-fluid py-4">

    <h3>Add New Driver</h3>

    <form action="{{ route('vendor.drivers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="name">Driver Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>

        <div class="form-group">
            <label for="license_number">License Number</label>
            <input type="text" class="form-control" id="license_number" name="license_number" required>
        </div>

        <div class="form-group">
            <label for="availability_status">Availability Status</label>
            <select class="form-control" name="availability_status" required>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </div>

        <div class="form-group">
            <label for="rating">Rating (Optional)</label>
            <input type="number" class="form-control" id="rating" name="rating" min="0" max="5">
        </div>

        <div class="form-group">
            <label for="image">Profile Image</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>

        <button type="submit" class="btn btn-success mt-3">Save Driver</button>
    </form>
</div>
@endsection
