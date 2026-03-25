<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\VendorSubscriptionService;

class VendorVehicleController extends Controller
{
    public function show(Vehicle $vehicle)
    {
        if ($vehicle->vendor_id !== Auth::id()) {
            abort(403);
        }

        $vehicle->load([
            'images',
            'services' => function ($query) {
                $query->latest('service_date');
            }
        ]);

        $latestService = $vehicle->services->first();
        $totalServiceCost = $vehicle->services->sum('cost');

        $serviceAlert = 'ok';

        if ($latestService) {
            if ($latestService->next_service_due_date && now()->gt($latestService->next_service_due_date)) {
                $serviceAlert = 'overdue';
            } elseif ($latestService->next_service_due_date && now()->diffInDays($latestService->next_service_due_date, false) <= 7) {
                $serviceAlert = 'due_soon';
            }
        }

        return view('vendor.pages.vehicles.show', compact(
            'vehicle',
            'latestService',
            'totalServiceCost',
            'serviceAlert'
        ));
    }

    public function index()
    {
        $vehicles = Vehicle::where('vendor_id', Auth::id())
            ->latest()
            ->paginate(10);

        $subscriptionSummary = app(VendorSubscriptionService::class)->getSummary(Auth::id());

        return view('vendor.pages.vehicles.index', compact('vehicles', 'subscriptionSummary'));
    }

    public function create()
    {
        $subscriptionService = app(VendorSubscriptionService::class);
        $subscriptionSummary = $subscriptionService->getSummary(Auth::id());
        $canAddVehicle = $subscriptionService->canAddVehicle(Auth::id());

        return view('vendor.pages.vehicles.create', compact('subscriptionSummary', 'canAddVehicle'));
    }

    public function store(Request $request)
    {
        $subscriptionService = app(VendorSubscriptionService::class);

        if (! $subscriptionService->canAddVehicle(Auth::id())) {
            return redirect()
                ->route('vendor.vehicles.index')
                ->with(
                    'error',
                    'Your current plan allows only ' . $subscriptionService->getVehicleLimit(Auth::id()) . ' active vehicles. Upgrade your subscription to add more vehicles.'
                );
        }

        $data = $request->validate([
            'wheel_type' => ['required'],
            'vehicle_type' => ['required'],
            'brand' => ['required'],
            'model' => ['required'],
            'registration_no' => ['required', 'unique:vehicles,registration_no'],
            'manufacture_year' => ['required', 'integer'],
            'fuel_type' => ['required', Rule::in(['petrol', 'diesel', 'electric'])],
            'transmission' => ['required'],
            'seating_capacity' => ['required', 'integer'],

            'mileage_per_litre' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(in_array($request->fuel_type, ['petrol', 'diesel']))
            ],
            'fuel_tank_capacity' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(in_array($request->fuel_type, ['petrol', 'diesel']))
            ],

            'battery_capacity' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf($request->fuel_type === 'electric')
            ],
            'range_per_charge' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf($request->fuel_type === 'electric')
            ],
            'charging_time' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf($request->fuel_type === 'electric')
            ],
            'charger_type' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf($request->fuel_type === 'electric')
            ],

            'price_per_day' => ['required', 'numeric', 'min:0'],
            'with_driver_price_per_day' => ['nullable', 'numeric', 'min:0'],

            'discount_15_days' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_30_days' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_60_days' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'description' => ['nullable', 'string'],
            'location_city' => ['required', 'string', 'max:100'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
        ]);

        $discount15 = (float) ($data['discount_15_days'] ?? 0);
        $discount30 = (float) ($data['discount_30_days'] ?? 0);
        $discount60 = (float) ($data['discount_60_days'] ?? 0);

        if ($discount15 > $discount30) {
            return back()
                ->withErrors([
                    'discount_15_days' => '15+ days discount cannot be greater than 30+ days discount.',
                ])
                ->withInput();
        }

        if ($discount30 > $discount60) {
            return back()
                ->withErrors([
                    'discount_30_days' => '30+ days discount cannot be greater than 60+ days discount.',
                ])
                ->withInput();
        }

        if (in_array($data['fuel_type'], ['petrol', 'diesel'])) {
            $data['battery_capacity'] = null;
            $data['range_per_charge'] = null;
            $data['charging_time'] = null;
            $data['charger_type'] = null;
        }

        if ($data['fuel_type'] === 'electric') {
            $data['mileage_per_litre'] = null;
            $data['fuel_tank_capacity'] = null;
        }

        $title = trim($data['brand'] . ' ' . $data['model']) . ' (' . strtoupper($data['vehicle_type']) . ')';

        $vehicle = Vehicle::create([
            'vendor_id' => Auth::id(),
            'title' => $title,
            'wheel_type' => $data['wheel_type'],
            'vehicle_type' => $data['vehicle_type'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'registration_no' => $data['registration_no'],
            'manufacture_year' => $data['manufacture_year'],
            'fuel_type' => $data['fuel_type'],
            'transmission' => $data['transmission'],
            'seating_capacity' => $data['seating_capacity'],

            'mileage_per_litre' => $data['mileage_per_litre'] ?? null,
            'fuel_tank_capacity' => $data['fuel_tank_capacity'] ?? null,

            'battery_capacity' => $data['battery_capacity'] ?? null,
            'range_per_charge' => $data['range_per_charge'] ?? null,
            'charging_time' => $data['charging_time'] ?? null,
            'charger_type' => $data['charger_type'] ?? null,

            'price_per_day' => $data['price_per_day'],
            'with_driver_price_per_day' => $data['with_driver_price_per_day'] ?? null,

            'discount_15_days' => $data['discount_15_days'] ?? 0,
            'discount_30_days' => $data['discount_30_days'] ?? 0,
            'discount_60_days' => $data['discount_60_days'] ?? 0,

            'description' => $data['description'] ?? null,
            'location_city' => $data['location_city'],

            'status' => 'available',
            'is_active' => true,
            'approved_by' => null,
            'approved_at' => null,
            'reject_reason' => null,
        ]);

        if ($request->hasFile('images')) {
            $isFirst = true;

            foreach ($request->file('images') as $img) {
                $path = $img->store('vehicles', 'public');

                $vehicle->images()->create([
                    'path' => $path,
                    'is_primary' => $isFirst,
                ]);

                if ($isFirst) {
                    $vehicle->update(['image_url' => $path]);
                    $isFirst = false;
                }
            }
        }

        return redirect()
            ->route('vendor.vehicles.index')
            ->with('success', 'Vehicle added successfully.');
    }


    public function edit(Vehicle $vehicle)
    {
        // Only own vehicle
        if ($vehicle->vendor_id !== Auth::id()) {
            abort(403);
        }

        return view('vendor.pages.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->vendor_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'wheel_type' => ['required'],
            'vehicle_type' => ['required'],
            'brand' => ['required'],
            'model' => ['required'],
            'registration_no' => [
                'required',
                Rule::unique('vehicles', 'registration_no')->ignore($vehicle->id),
            ],
            'manufacture_year' => ['required', 'integer'],
            'fuel_type' => ['required', Rule::in(['petrol', 'diesel', 'electric'])],
            'transmission' => ['required'],
            'seating_capacity' => ['required', 'integer'],

            'mileage_per_litre' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(in_array($request->fuel_type, ['petrol', 'diesel']))
            ],
            'fuel_tank_capacity' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(in_array($request->fuel_type, ['petrol', 'diesel']))
            ],

            'battery_capacity' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf($request->fuel_type === 'electric')
            ],
            'range_per_charge' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf($request->fuel_type === 'electric')
            ],
            'charging_time' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf($request->fuel_type === 'electric')
            ],
            'charger_type' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf($request->fuel_type === 'electric')
            ],

            'price_per_day' => ['required', 'numeric', 'min:0'],
            'with_driver_price_per_day' => ['nullable', 'numeric'],

            'discount_15_days' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_30_days' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_60_days' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'location_city' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],

            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:2048'],
        ]);

        $discountOrderError = $this->validateDurationDiscountOrder($data);

        if ($discountOrderError) {
            return back()->withErrors([
                'discount_15_days' => $discountOrderError,
            ])->withInput();
        }

        if (in_array($data['fuel_type'], ['petrol', 'diesel'])) {
            $data['battery_capacity'] = null;
            $data['range_per_charge'] = null;
            $data['charging_time'] = null;
            $data['charger_type'] = null;
        }

        if ($data['fuel_type'] === 'electric') {
            $data['mileage_per_litre'] = null;
            $data['fuel_tank_capacity'] = null;
        }

        $title = trim($data['brand'] . ' ' . $data['model']) . ' (' . strtoupper($data['vehicle_type']) . ')';

        $vehicle->update([
            'title' => $title,
            'wheel_type' => $data['wheel_type'],
            'vehicle_type' => $data['vehicle_type'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'registration_no' => $data['registration_no'],
            'manufacture_year' => $data['manufacture_year'],
            'fuel_type' => $data['fuel_type'],
            'transmission' => $data['transmission'],
            'seating_capacity' => $data['seating_capacity'],

            'mileage_per_litre' => $data['mileage_per_litre'] ?? null,
            'fuel_tank_capacity' => $data['fuel_tank_capacity'] ?? null,
            'battery_capacity' => $data['battery_capacity'] ?? null,
            'range_per_charge' => $data['range_per_charge'] ?? null,
            'charging_time' => $data['charging_time'] ?? null,
            'charger_type' => $data['charger_type'] ?? null,

            'price_per_day' => $data['price_per_day'],
            'with_driver_price_per_day' => $data['with_driver_price_per_day'] ?? null,

            'discount_15_days' => $data['discount_15_days'] ?? 0,
            'discount_30_days' => $data['discount_30_days'] ?? 0,
            'discount_60_days' => $data['discount_60_days'] ?? 0,

            'location_city' => $data['location_city'],
            'description' => $data['description'] ?? null,

            // re-approval workflow
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'reject_reason' => null,
        ]);

        // Append new images
        if ($request->hasFile('images')) {
            $hasPrimary = $vehicle->images()->where('is_primary', true)->exists();

            foreach ($request->file('images') as $img) {
                $path = $img->store('vehicles', 'public');

                $vehicle->images()->create([
                    'path' => $path,
                    'is_primary' => ! $hasPrimary,
                ]);

                if (! $hasPrimary) {
                    $vehicle->update(['image_url' => $path]);
                    $hasPrimary = true;
                }
            }
        }

        return redirect()
            ->route('vendor.vehicles.index')
            ->with('success', 'Vehicle updated successfully and sent for re-approval.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->vendor_id !== Auth::id()) {
            abort(403);
        }

        // delete image files
        foreach ($vehicle->images as $img) {
            if ($img->path && Storage::disk('public')->exists($img->path)) {
                Storage::disk('public')->delete($img->path);
            }
        }

        $vehicle->delete();

        return back()->with('success', 'Vehicle deleted successfully.');
    }

    private function validateDurationDiscountOrder(array $data)
    {
        $discount15 = (float) ($data['discount_15_days'] ?? 0);
        $discount30 = (float) ($data['discount_30_days'] ?? 0);
        $discount60 = (float) ($data['discount_60_days'] ?? 0);

        if ($discount15 > $discount30) {
            return '15+ days discount cannot be greater than 30+ days discount.';
        }

        if ($discount30 > $discount60) {
            return '30+ days discount cannot be greater than 60+ days discount.';
        }

        return null;
    }
}
