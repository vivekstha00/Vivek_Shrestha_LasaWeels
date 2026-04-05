@extends('vendor.layouts.master')

@section('title', 'Vehicles - Vendor')
@section('page_title', 'Vehicles - Create')
@section('page_subtitle', 'Add a new vehicle listing')

@section('vendor-content')
<div class="container-fluid py-4">

    <h3 class="mb-4">Create Vehicle</h3>
    <div class="alert alert-info rounded-3">
        New vehicles require admin approval before going live. Please upload valid registration and insurance documents.
    </div>

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

                    <div class="col-md-6">
                        <label class="form-label">Wheel Type *</label>
                        <select name="wheel_type" id="wheel_type" class="form-select" required>
                            <option value="4_wheeler" {{ old('wheel_type','4_wheeler') == '4_wheeler' ? 'selected' : '' }}>4 Wheeler</option>
                            <option value="2_wheeler" {{ old('wheel_type') == '2_wheeler' ? 'selected' : '' }}>2 Wheeler</option>
                        </select>
                        @error('wheel_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vehicle Type *</label>
                        <select name="vehicle_type" id="vehicle_type" class="form-select" required></select>
                        @error('vehicle_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fuel Type *</label>
                        <select name="fuel_type" id="fuel_type" class="form-select" required>
                            <option value="">Select Fuel Type</option>
                            <option value="petrol" {{ old('fuel_type') == 'petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="electric" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>Electric</option>
                        </select>
                        @error('fuel_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6" id="transmissionGroup">
                        <label class="form-label">Transmission *</label>
                        <select name="transmission" id="transmission" class="form-select">
                            <option value="manual" {{ old('transmission') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="automatic" {{ old('transmission') == 'automatic' ? 'selected' : '' }}>Automatic</option>
                        </select>
                        @error('transmission') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Brand *</label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" required>
                        @error('brand') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model') }}" required>
                        @error('model') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">City *</label>
                        <input type="text" name="location_city" class="form-control" value="{{ old('location_city') }}" required>
                        @error('location_city') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Registration No *</label>
                        <input type="text" name="registration_no" class="form-control" value="{{ old('registration_no') }}" required>
                        @error('registration_no') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Manufacture Year *</label>
                        <input type="number" name="manufacture_year" class="form-control"
                               min="1990" max="{{ date('Y') + 1 }}"
                               value="{{ old('manufacture_year') }}" required>
                        @error('manufacture_year') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>



                    <div class="col-md-6" id="seatingGroup">
                        <label class="form-label">Seating Capacity *</label>
                        <input type="number" name="seating_capacity" id="seating_capacity"
                               class="form-control" value="{{ old('seating_capacity',5) }}">
                        @error('seating_capacity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div id="fuelFields" class="row g-3 m-0 p-0">
                        <div class="col-md-6">
                            <label class="form-label">Mileage per Litre</label>
                            <input type="number" step="0.01" name="mileage_per_litre" class="form-control"
                                   value="{{ old('mileage_per_litre') }}">
                            @error('mileage_per_litre') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fuel Tank Capacity (L)</label>
                            <input type="number" step="0.01" name="fuel_tank_capacity" class="form-control"
                                   value="{{ old('fuel_tank_capacity') }}">
                            @error('fuel_tank_capacity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div id="electricFields" class="row g-3 m-0 p-0">
                        <div class="col-md-6">
                            <label class="form-label">Battery Capacity (kWh)</label>
                            <input type="number" step="0.01" name="battery_capacity" class="form-control"
                                   value="{{ old('battery_capacity') }}">
                            @error('battery_capacity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Range per Charge (km)</label>
                            <input type="number" step="0.01" name="range_per_charge" class="form-control"
                                   value="{{ old('range_per_charge') }}">
                            @error('range_per_charge') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Charging Time (hours)</label>
                            <input type="number" step="0.01" name="charging_time" class="form-control"
                                   value="{{ old('charging_time') }}">
                            @error('charging_time') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Charger Type</label>
                            <input type="text" name="charger_type" class="form-control"
                                   value="{{ old('charger_type') }}" placeholder="Type 2 / CCS / Fast Charging">
                            @error('charger_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Price per Day *</label>
                        <input type="number" step="0.01" name="price_per_day" class="form-control"
                               value="{{ old('price_per_day') }}" required>
                        @error('price_per_day') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6" id="driverPriceGroup">
                        <label class="form-label">With Driver Price per Day</label>
                        <input type="number" step="0.01" name="with_driver_price_per_day" class="form-control"
                               value="{{ old('with_driver_price_per_day') }}">
                        @error('with_driver_price_per_day') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">Vehicle Images</label>
                        <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                        @error('images') <small class="text-danger">{{ $message }}</small> @enderror
                        @error('images.*') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vehicle Registration Document *</label>
                        <input type="file" name="vehicle_registration_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        @error('vehicle_registration_document') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Insurance Document *</label>
                        <input type="file" name="insurance_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        @error('insurance_document') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Insurance Expiry Date *</label>
                        <input type="date" name="insurance_expiry_date" class="form-control" value="{{ old('insurance_expiry_date') }}" required>
                        @error('insurance_expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-12">
                        <div class="card border rounded-3 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Long Duration Discount (Vendor Pricing Rule)</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">15+ Days Discount (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                               name="discount_15_days" class="form-control"
                                               value="{{ old('discount_15_days', 0) }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">30+ Days Discount (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                               name="discount_30_days" class="form-control"
                                               value="{{ old('discount_30_days', 0) }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">60+ Days Discount (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                               name="discount_60_days" class="form-control"
                                               value="{{ old('discount_60_days', 0) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
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
    const vehicleTypeOptions = {
        '4_wheeler': [
            { value: 'car', label: 'Car' },
            { value: 'suv', label: 'SUV' },
            { value: 'pickup', label: 'Pickup' },
            { value: 'jeep', label: 'Jeep' },
            { value: 'van', label: 'Van' },
            { value: 'ev', label: 'EV Car' }
        ],
        '2_wheeler': [
            { value: 'bike', label: 'Bike' },
            { value: 'scooter', label: 'Scooter' }
        ]
    };

    function populateVehicleTypes(selectedValue = '') {
        const wheelType = document.getElementById('wheel_type').value;
        const vehicleTypeSelect = document.getElementById('vehicle_type');
        const options = vehicleTypeOptions[wheelType] || [];

        vehicleTypeSelect.innerHTML = '<option value="">Select</option>';

        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.value;
            opt.textContent = option.label;
            if (selectedValue === option.value) {
                opt.selected = true;
            }
            vehicleTypeSelect.appendChild(opt);
        });
    }

    function toggleFuelFields() {
        const fuelType = document.getElementById('fuel_type').value;
        const fuelFields = document.getElementById('fuelFields');
        const electricFields = document.getElementById('electricFields');
        const transmissionSelect = document.getElementById('transmission');

        if (fuelType === 'electric') {
            fuelFields.style.display = 'none';
            electricFields.style.display = 'flex';
            if (document.getElementById('wheel_type').value === '2_wheeler') {
                transmissionSelect.value = 'automatic';
            }
        } else if (fuelType === 'petrol' || fuelType === 'diesel') {
            fuelFields.style.display = 'flex';
            electricFields.style.display = 'none';
        } else {
            fuelFields.style.display = 'none';
            electricFields.style.display = 'none';
        }
    }

    function toggleFuelTypeOptions() {
        const wheelType = document.getElementById('wheel_type').value;
        const fuelTypeSelect = document.getElementById('fuel_type');
        const dieselOption = fuelTypeSelect.querySelector('option[value="diesel"]');

        if (!dieselOption) {
            return;
        }

        const shouldHideDiesel = wheelType === '2_wheeler';

        dieselOption.hidden = shouldHideDiesel;
        dieselOption.disabled = shouldHideDiesel;

        if (shouldHideDiesel && fuelTypeSelect.value === 'diesel') {
            fuelTypeSelect.value = '';
        }
    }

    function toggleWheelTypeFields() {
        const wheelType = document.getElementById('wheel_type').value;
        const driverPriceGroup = document.getElementById('driverPriceGroup');
        const seatingGroup = document.getElementById('seatingGroup');
        const transmissionGroup = document.getElementById('transmissionGroup');
        const transmissionSelect = document.getElementById('transmission');
        const fuelType = document.getElementById('fuel_type').value;

        populateVehicleTypes(document.getElementById('vehicle_type').dataset.selected || '');
    toggleFuelTypeOptions();

        if (wheelType === '2_wheeler') {
            driverPriceGroup.style.display = 'none';
            seatingGroup.style.display = 'none';
            transmissionGroup.style.display = 'none';

            if (fuelType === 'electric') {
                transmissionSelect.value = 'automatic';
            } else {
                transmissionSelect.value = 'manual';
            }
        } else {
            driverPriceGroup.style.display = 'block';
            seatingGroup.style.display = 'block';
            transmissionGroup.style.display = 'block';
        }

        toggleFuelFields();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const vehicleTypeSelect = document.getElementById('vehicle_type');
        vehicleTypeSelect.dataset.selected = @json(old('vehicle_type'));

        toggleFuelTypeOptions();
        populateVehicleTypes(vehicleTypeSelect.dataset.selected);
        toggleWheelTypeFields();
        toggleFuelFields();

        document.getElementById('wheel_type').addEventListener('change', function () {
            vehicleTypeSelect.dataset.selected = '';
            toggleWheelTypeFields();
        });

        document.getElementById('fuel_type').addEventListener('change', function () {
            toggleFuelFields();
            toggleWheelTypeFields();
        });
    });
</script>
@endsection
