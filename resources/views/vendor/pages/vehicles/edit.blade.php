@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container-fluid py-4">

    <h3 class="mb-4">Edit Vehicle</h3>

    <form method="POST"
          action="{{ route('vendor.vehicles.update',$vehicle->id) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card shadow-sm">
            <div class="card-header">
                <strong>General</strong>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    {{-- Wheel Type --}}
                    <div class="col-md-6">
                        <label class="form-label">Wheel Type *</label>
                        <select name="wheel_type" class="form-select" required>
                            @foreach(config('vehicle.wheel_types') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('wheel_type',$vehicle->wheel_type) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Vehicle Type --}}
                    <div class="col-md-6">
                        <label class="form-label">Vehicle Type *</label>
                        <select name="vehicle_type" class="form-select" required>
                            @foreach(config('vehicle.vehicle_types') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('vehicle_type',$vehicle->vehicle_type) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Brand --}}
                    <div class="col-md-6">
                        <label class="form-label">Brand *</label>
                        <input type="text" name="brand" class="form-control"
                               value="{{ old('brand',$vehicle->brand) }}" required>
                    </div>

                    {{-- Model --}}
                    <div class="col-md-6">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" class="form-control"
                               value="{{ old('model',$vehicle->model) }}" required>
                    </div>

                    {{-- Registration --}}
                    <div class="col-md-6">
                        <label class="form-label">Registration No *</label>
                        <input type="text" name="registration_no" class="form-control"
                               value="{{ old('registration_no',$vehicle->registration_no) }}" required>
                    </div>

                    {{-- Manufacture Year --}}
                    <div class="col-md-6">
                        <label class="form-label">Manufacture Year *</label>
                        <input type="number" name="manufacture_year"
                               class="form-control"
                               value="{{ old('manufacture_year',$vehicle->manufacture_year) }}"
                               min="1990" max="{{ date('Y')+1 }}" required>
                    </div>

                    {{-- Fuel --}}
                    <div class="col-md-6">
                        <label class="form-label">Fuel Type *</label>
                        <select name="fuel_type" class="form-select" required>
                            @foreach(config('vehicle.fuel_types') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('fuel_type',$vehicle->fuel_type) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Transmission --}}
                    <div class="col-md-6">
                        <label class="form-label">Transmission *</label>
                        <select name="transmission" class="form-select" required>
                            @foreach(config('vehicle.transmissions') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('transmission',$vehicle->transmission) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Seating --}}
                    <div class="col-md-6">
                        <label class="form-label">Seating Capacity *</label>
                        <input type="number" name="seating_capacity"
                               class="form-control"
                               value="{{ old('seating_capacity',$vehicle->seating_capacity) }}" required>
                    </div>

                    {{-- Mileage --}}
                    <div class="col-md-6">
                        <label class="form-label">Mileage per Litre</label>
                        <input type="number" step="0.01"
                               name="mileage_per_litre"
                               class="form-control"
                               value="{{ old('mileage_per_litre',$vehicle->mileage_per_litre) }}">
                    </div>

                    {{-- Price --}}
                    <div class="col-md-6">
                        <label class="form-label">Price per Day *</label>
                        <input type="number" step="0.01"
                               name="price_per_day"
                               class="form-control"
                               value="{{ old('price_per_day',$vehicle->price_per_day) }}" required>
                    </div>

                    {{-- With Driver --}}
                    <div class="col-md-6">
                        <label class="form-label">With Driver Price per Day</label>
                        <input type="number" step="0.01"
                               name="with_driver_price_per_day"
                               class="form-control"
                               value="{{ old('with_driver_price_per_day',$vehicle->with_driver_price_per_day) }}">
                    </div>

                    {{-- Images --}}
                    <div class="col-md-6">
                        <label class="form-label">Add More Images</label>
                        <input type="file" name="images[]" multiple
                               class="form-control" accept="image/*">
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            @foreach(config('vehicle.status_options') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('status',$vehicle->status) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="4">{{ old('description',$vehicle->description) }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">Update Vehicle</button>
            <a href="{{ route('vendor.vehicles.index') }}" class="btn btn-outline-dark">Cancel</a>
        </div>

    </form>

</div>
@endsection
