@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container-fluid py-4">
    <h3 class="mb-3">Edit Vehicle</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('vendor.vehicles.update', $vehicle->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input name="title" class="form-control" value="{{ old('title', $vehicle->title) }}" required>
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vehicle Type</label>
                        <input name="vehicle_type" class="form-control" value="{{ old('vehicle_type', $vehicle->vehicle_type) }}" required>
                        @error('vehicle_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <input name="brand" class="form-control" value="{{ old('brand', $vehicle->brand) }}" required>
                        @error('brand') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Model</label>
                        <input name="model" class="form-control" value="{{ old('model', $vehicle->model) }}" required>
                        @error('model') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Registration No</label>
                        <input name="registration_no" class="form-control" value="{{ old('registration_no', $vehicle->registration_no) }}" required>
                        @error('registration_no') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fuel Type</label>
                        <input name="fuel_type" class="form-control" value="{{ old('fuel_type', $vehicle->fuel_type) }}" required>
                        @error('fuel_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Transmission</label>
                        <input name="transmission" class="form-control" value="{{ old('transmission', $vehicle->transmission) }}" required>
                        @error('transmission') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Seats</label>
                        <input type="number" name="seating_capacity" class="form-control"
                               value="{{ old('seating_capacity', $vehicle->seating_capacity) }}" required>
                        @error('seating_capacity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Price / Day</label>
                        <input type="number" step="0.01" name="price_per_day" class="form-control"
                               value="{{ old('price_per_day', $vehicle->price_per_day) }}" required>
                        @error('price_per_day') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input name="location_city" class="form-control" value="{{ old('location_city', $vehicle->location_city) }}" required>
                        @error('location_city') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $vehicle->description) }}</textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Replace Image (optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror

                        @if($vehicle->image_url)
                            <div class="small text-muted mt-2">Current: {{ $vehicle->image_url }}</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('vendor.vehicles.index') }}" class="btn btn-outline-dark">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
