<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorVehicleServiceController extends Controller
{
    protected function authorizeVehicle(Vehicle $vehicle): void
    {
        if ($vehicle->vendor_id !== Auth::id()) {
            abort(403);
        }
    }

    protected function authorizeService(Vehicle $vehicle, VehicleService $service): void
    {
        $this->authorizeVehicle($vehicle);

        if ($service->vehicle_id !== $vehicle->id) {
            abort(404);
        }
    }

    public function create(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        return view('vendor.pages.vehicles.services.create', compact('vehicle'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $data = $request->validate([
            'service_date' => ['required', 'date'],
            'service_items' => ['required', 'array', 'min:1'],
            'service_items.*' => ['required', 'string', 'max:100'],
            'custom_items' => ['nullable', 'string'],
            'odometer_km' => ['nullable', 'integer', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'next_service_due_date' => ['nullable', 'date', 'after_or_equal:service_date'],
            'next_service_due_km' => ['nullable', 'integer', 'min:0'],
        ]);

        // Build service_type from selected items + custom items
        $allItems = $data['service_items'];
        if (!empty($data['custom_items'])) {
            $customItems = array_filter(array_map('trim', explode(',', $data['custom_items'])));
            $allItems = array_merge($allItems, $customItems);
        }

        $service = $vehicle->services()->create([
            'service_date' => $data['service_date'],
            'odometer_km' => $data['odometer_km'] ?? null,
            'cost' => $data['cost'] ?? null,
            'notes' => $data['notes'] ?? null,
            'next_service_due_date' => $data['next_service_due_date'] ?? null,
            'next_service_due_km' => $data['next_service_due_km'] ?? null,
            'service_type' => implode(', ', $allItems),
        ]);

        // save standard items
        foreach ($data['service_items'] as $item) {
            $service->items()->create([
                'service_item' => $item,
                'is_custom' => false,
            ]);
        }

        // save custom items
        if (!empty($data['custom_items'])) {
            $customItems = array_filter(array_map('trim', explode(',', $data['custom_items'])));

            foreach ($customItems as $item) {
                $service->items()->create([
                    'service_item' => $item,
                    'is_custom' => true,
                ]);
            }
        }

        return redirect()
            ->route('vendor.vehicles.show', $vehicle->id)
            ->with('success', 'Service record added successfully.');
    }

    public function edit(Vehicle $vehicle, VehicleService $service)
    {
        $this->authorizeService($vehicle, $service);

        $service->load('items');

        $customItemsText = $service->items
            ->where('is_custom', true)
            ->pluck('service_item')
            ->implode(', ');

        return view('vendor.pages.vehicles.services.edit', compact('vehicle', 'service', 'customItemsText'));
    }

    public function update(Request $request, Vehicle $vehicle, VehicleService $service)
    {
        $this->authorizeService($vehicle, $service);

        $data = $request->validate([
            'service_date' => ['required', 'date'],
            'service_items' => ['required', 'array', 'min:1'],
            'service_items.*' => ['required', 'string', 'max:100'],
            'custom_items' => ['nullable', 'string'],
            'odometer_km' => ['nullable', 'integer', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'next_service_due_date' => ['nullable', 'date', 'after_or_equal:service_date'],
            'next_service_due_km' => ['nullable', 'integer', 'min:0'],
        ]);

        // Build service_type from selected items + custom items
        $allItems = $data['service_items'];
        if (!empty($data['custom_items'])) {
            $customParsed = array_filter(array_map('trim', explode(',', $data['custom_items'])));
            $allItems = array_merge($allItems, $customParsed);
        }

        $service->update([
            'service_date' => $data['service_date'],
            'odometer_km' => $data['odometer_km'] ?? null,
            'cost' => $data['cost'] ?? null,
            'notes' => $data['notes'] ?? null,
            'next_service_due_date' => $data['next_service_due_date'] ?? null,
            'next_service_due_km' => $data['next_service_due_km'] ?? null,
            'service_type' => implode(', ', $allItems),
        ]);

        // delete old items
        $service->items()->delete();

        // save standard items
        foreach ($data['service_items'] as $item) {
            $service->items()->create([
                'service_item' => $item,
                'is_custom' => false,
            ]);
        }

        // save custom items
        if (!empty($data['custom_items'])) {
            $customItems = array_filter(array_map('trim', explode(',', $data['custom_items'])));

            foreach ($customItems as $item) {
                $service->items()->create([
                    'service_item' => $item,
                    'is_custom' => true,
                ]);
            }
        }

        return redirect()
            ->route('vendor.vehicles.show', $vehicle->id)
            ->with('success', 'Service record updated successfully.');
    }

    public function destroy(Vehicle $vehicle, VehicleService $service)
    {
        $this->authorizeService($vehicle, $service);

        $service->delete();

        return redirect()
            ->route('vendor.vehicles.show', $vehicle->id)
            ->with('success', 'Service record deleted successfully.');
    }
}
