@extends('vendor.layouts.master')

@section('title', 'Edit Service Record')
@section('page_title', 'Edit Service Record')
@section('page_subtitle', 'Update maintenance details for this vehicle')

@section('vendor-content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Edit Service Record</h3>
            <div class="text-muted small">{{ $vehicle->title }}</div>
        </div>

        <a href="{{ route('vendor.vehicles.show', $vehicle->id) }}" class="btn btn-outline-secondary">
            Back
        </a>
    </div>

    @php
        $serviceItems = [
            'oil_change' => 'Oil Change',
            'tyre_change' => 'Tyre Change',
            'brake_service' => 'Brake Service',
            'engine_service' => 'Engine Service',
            'full_service' => 'Full Service',
            'battery_change' => 'Battery Change',
            'general_checkup' => 'General Checkup',
        ];

        $selectedItems = old(
            'service_items',
            $service->items->where('is_custom', false)->pluck('service_item')->toArray()
        );
    @endphp

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('vendor.vehicles.services.update', [$vehicle->id, $service->id]) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service Date *</label>
                        <input type="date" name="service_date" class="form-control"
                               value="{{ old('service_date', optional($service->service_date)->format('Y-m-d')) }}" required>
                        @error('service_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Service Items *</label>
                        <div class="row">
                            @foreach($serviceItems as $value => $label)
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="service_items[]"
                                               value="{{ $value }}"
                                               id="item_{{ $value }}"
                                               {{ in_array($value, $selectedItems) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="item_{{ $value }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('service_items') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        @error('service_items.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Other Work Done (optional)</label>
                        <input type="text" name="custom_items" class="form-control"
                               value="{{ old('custom_items', $customItemsText ?? '') }}"
                               placeholder="e.g. Air filter change, AC repair">
                        <small class="text-muted">Separate multiple custom items with commas.</small>
                        @error('custom_items') <small class="text-danger d-block">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Odometer (KM)</label>
                        <input type="number" name="odometer_km" class="form-control"
                               value="{{ old('odometer_km', $service->odometer_km) }}">
                        @error('odometer_km') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Cost</label>
                        <input type="number" step="0.01" name="cost" class="form-control"
                               value="{{ old('cost', $service->cost) }}">
                        @error('cost') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Next Service Due Date</label>
                        <input type="date" name="next_service_due_date" class="form-control"
                               value="{{ old('next_service_due_date', optional($service->next_service_due_date)->format('Y-m-d')) }}">
                        @error('next_service_due_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Next Service Due KM</label>
                        <input type="number" name="next_service_due_km" class="form-control"
                               value="{{ old('next_service_due_km', $service->next_service_due_km) }}">
                        @error('next_service_due_km') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $service->notes) }}</textarea>
                        @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-primary">Update Service Record</button>
                    <a href="{{ route('vendor.vehicles.show', $vehicle->id) }}" class="btn btn-outline-dark">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
