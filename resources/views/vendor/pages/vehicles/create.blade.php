@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container py-4">
    <h4 class="mb-4">Add New Vehicle</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('vendor.vehicles.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vehicle Type</label>
                        <input type="text" name="vehicle_type" class="form-control" placeholder="SUV, Sedan, Hatchback" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Registration No</label>
                        <input type="text" name="registration_no" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Fuel Type</label>
                        <select name="fuel_type" class="form-select" required>
                            <option value="">Select</option>
                            <option>Petrol</option>
                            <option>Diesel</option>
                            <option>Electric</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Transmission</label>
                        <select name="transmission" class="form-select" required>
                            <option value="">Select</option>
                            <option>Manual</option>
                            <option>Automatic</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Price Per Day (NPR)</label>
                        <input type="number" name="price_per_day" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="location_city" class="form-control" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        Save Vehicle
                    </button>
                    <a href="{{ route('vendor.vehicles.index') }}" class="btn btn-secondary ms-2">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
