@extends('vendor.layouts.master')

@section('title', 'Vehicles - Vendor')
@section('page_title', 'Vehicles - Create')
@section('page_subtitle', 'Add a new vehicle listing')

@section('vendor-content')
<div class="container-fluid py-4">

    <h3 class="mb-4">Create Vehicle</h3>

    @if(isset($subscriptionSummary))
        <div class="alert {{ ($canAddVehicle ?? false) ? 'alert-info' : 'alert-warning' }} rounded-3 mb-4">
            <strong>Current Plan:</strong> {{ $subscriptionSummary['plan_name'] ?? 'Free Plan' }}<br>
            <strong>Vehicle Usage:</strong> {{ $subscriptionSummary['vehicle_count'] ?? 0 }} / {{ $subscriptionSummary['vehicle_limit'] ?? 2 }}

            @if(!($canAddVehicle ?? true))
                <hr class="my-2">
                Free plan limit reached. Upgrade subscription to add more vehicles.
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.vehicles.store') }}" enctype="multipart/form-data">
        @csrf

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
                                    {{ old('wheel_type','4_wheeler') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('wheel_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Vehicle Type --}}
                    <div class="col-md-6">
                        <label class="form-label">Vehicle Type *</label>
                        <select name="vehicle_type" class="form-select" required>
                            <option value="">Select</option>
                            @foreach(config('vehicle.vehicle_types') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('vehicle_type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Brand --}}
                    <div class="col-md-6">
                        <label class="form-label">Brand *</label>
                        <input type="text" name="brand" class="form-control"
                               value="{{ old('brand') }}" required>
                        @error('brand') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Model --}}
                    <div class="col-md-6">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" class="form-control"
                               value="{{ old('model') }}" required>
                        @error('model') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- City --}}
                    <div class="col-md-6">
                        <label class="form-label">City *</label>
                        <input type="text" name="location_city" class="form-control"
                            value="{{ old('location_city') }}" required>
                        @error('location_city') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Registration No --}}
                    <div class="col-md-6">
                        <label class="form-label">Registration No *</label>
                        <input type="text" name="registration_no" class="form-control"
                               value="{{ old('registration_no') }}" required>
                        @error('registration_no') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Manufacture Year --}}
                    <div class="col-md-6">
                        <label class="form-label">Manufacture Year *</label>
                        <input type="number" name="manufacture_year" class="form-control"
                               min="1990" max="{{ date('Y') + 1 }}"
                               value="{{ old('manufacture_year') }}" required>
                        @error('manufacture_year') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Fuel Type --}}
                    <div class="col-md-6">
                        <label class="form-label">Fuel Type *</label>
                        <select name="fuel_type" id="fuel_type" class="form-select" required>
                            <option value="">Select Fuel Type</option>
                            @foreach(config('vehicle.fuel_types') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('fuel_type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('fuel_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Transmission --}}
                    <div class="col-md-6">
                        <label class="form-label">Transmission *</label>
                        <select name="transmission" class="form-select" required>
                            @foreach(config('vehicle.transmissions') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('transmission') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('transmission') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Seating Capacity --}}
                    <div class="col-md-6">
                        <label class="form-label">Seating Capacity *</label>
                        <input type="number" name="seating_capacity"
                               class="form-control"
                               value="{{ old('seating_capacity',5) }}" required>
                        @error('seating_capacity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Petrol / Diesel Fields --}}
                    <div id="fuelFields" class="row g-3 m-0 p-0">
                        <div class="col-md-6">
                            <label class="form-label">Mileage per Litre</label>
                            <input type="number" step="0.01"
                                   name="mileage_per_litre"
                                   class="form-control"
                                   value="{{ old('mileage_per_litre') }}">
                            @error('mileage_per_litre') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fuel Tank Capacity (L)</label>
                            <input type="number" step="0.01"
                                   name="fuel_tank_capacity"
                                   class="form-control"
                                   value="{{ old('fuel_tank_capacity') }}">
                            @error('fuel_tank_capacity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Electric Fields --}}
                    <div id="electricFields" class="row g-3 m-0 p-0">
                        <div class="col-md-6">
                            <label class="form-label">Battery Capacity (kWh)</label>
                            <input type="number" step="0.01"
                                   name="battery_capacity"
                                   class="form-control"
                                   value="{{ old('battery_capacity') }}">
                            @error('battery_capacity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Range per Charge (km)</label>
                            <input type="number" step="0.01"
                                   name="range_per_charge"
                                   class="form-control"
                                   value="{{ old('range_per_charge') }}">
                            @error('range_per_charge') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Charging Time (hours)</label>
                            <input type="number" step="0.01"
                                   name="charging_time"
                                   class="form-control"
                                   value="{{ old('charging_time') }}">
                            @error('charging_time') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Charger Type</label>
                            <input type="text"
                                   name="charger_type"
                                   class="form-control"
                                   value="{{ old('charger_type') }}"
                                   placeholder="Type 2 / CCS / Fast Charging">
                            @error('charger_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Price per Day --}}
                    <div class="col-md-6">
                        <label class="form-label">Price per Day *</label>
                        <input type="number" step="0.01"
                               name="price_per_day"
                               class="form-control"
                               value="{{ old('price_per_day') }}" required>
                        @error('price_per_day') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- With Driver Price --}}
                    <div class="col-md-6">
                        <label class="form-label">With Driver Price per Day</label>
                        <input type="number" step="0.01"
                               name="with_driver_price_per_day"
                               class="form-control"
                               value="{{ old('with_driver_price_per_day') }}">
                        @error('with_driver_price_per_day') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12">
                        <div class="card border rounded-3 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Long Duration Discount (Vendor Pricing Rule)</h6>
                                <p class="text-muted small mb-3">
                                    These discounts are applied automatically for long bookings. Use 0 if you do not want to offer a discount.
                                </p>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">15+ Days Discount (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="discount_15_days"
                                            class="form-control"
                                            value="{{ old('discount_15_days', 0) }}">
                                        @error('discount_15_days') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">30+ Days Discount (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="discount_30_days"
                                            class="form-control"
                                            value="{{ old('discount_30_days', 0) }}">
                                        @error('discount_30_days') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">60+ Days Discount (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="discount_60_days"
                                            class="form-control"
                                            value="{{ old('discount_60_days', 0) }}">
                                        @error('discount_60_days') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <small class="text-muted d-block mt-2">
                                    Recommended order: 15+ days ≤ 30+ days ≤ 60+ days
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Images --}}
                    <div class="col-md-6">
                        <label class="form-label">Vehicle Images</label>
                        <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                        @error('images') <small class="text-danger">{{ $message }}</small> @enderror
                        @error('images.*') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            @foreach(config('vehicle.status_options') as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('status','available') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="4">{{ old('description') }}</textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" {{ !($canAddVehicle ?? true) ? 'disabled' : '' }}>
                Save Vehicle
            </button>
            <a href="{{ route('vendor.vehicles.index') }}" class="btn btn-outline-dark">Cancel</a>
        </div>
    </form>

</div>

<script>
    function toggleFuelFields() {
        const fuelType = document.getElementById('fuel_type').value;
        const fuelFields = document.getElementById('fuelFields');
        const electricFields = document.getElementById('electricFields');
        const transmissionSelect = document.querySelector('select[name="transmission"]');

        if (fuelType === 'electric') {
            fuelFields.style.display = 'none';
            electricFields.style.display = 'flex';
            transmissionSelect.value = 'automatic';
        } else if (fuelType === 'petrol' || fuelType === 'diesel') {
            fuelFields.style.display = 'flex';
            electricFields.style.display = 'none';
        } else {
            fuelFields.style.display = 'none';
            electricFields.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleFuelFields();
        document.getElementById('fuel_type').addEventListener('change', toggleFuelFields);
    });
</script>
@endsection
