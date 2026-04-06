<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\VendorSubscriptionService;
use App\Notifications\VehicleApprovalRequestToAdminNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

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
            'wheel_type' => ['required', Rule::in(['2_wheeler', '4_wheeler'])],
            'vehicle_type' => ['required', 'string', 'max:50'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'registration_no' => ['required', 'string', 'max:100', 'unique:vehicles,registration_no'],
            'manufacture_year' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'fuel_type' => ['required', Rule::in(['petrol', 'diesel', 'electric'])],
            'transmission' => ['nullable', 'string', 'max:50'],
            'seating_capacity' => ['nullable', 'integer', 'min:1', 'max:12'],

            'mileage_per_litre' => ['nullable', 'numeric', 'min:0'],
            'fuel_tank_capacity' => ['nullable', 'numeric', 'min:0'],
            'battery_capacity' => ['nullable', 'numeric', 'min:0'],
            'range_per_charge' => ['nullable', 'numeric', 'min:0'],
            'charging_time' => ['nullable', 'numeric', 'min:0'],
            'charger_type' => ['nullable', 'string', 'max:100'],

            'price_per_day' => ['required', 'numeric', 'min:0'],
            'with_driver_price_per_day' => ['nullable', 'numeric', 'min:0'],

            'discount_15_days' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_30_days' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_60_days' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'description' => ['nullable', 'string'],
            'location_city' => ['required', 'string', 'max:100'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,jfif,avif', 'max:10240'],
            'vehicle_registration_document' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,jfif,pdf', 'max:5120'],
            'insurance_document' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,jfif,pdf', 'max:5120'],
            'insurance_expiry_date' => ['required', 'date', 'after_or_equal:today'],
            'road_tax_document' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,jfif,pdf', 'max:5120'],
            'road_tax_expiry_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'images.*.image' => 'Each vehicle image must be a valid image file.',
            'images.*.mimes' => 'Vehicle images must be jpg, jpeg, png, webp, jfif, or avif.',
            'images.*.uploaded' => 'One of the selected vehicle images failed to upload. Please reduce file size and try again.',
            'images.*.max' => 'Each vehicle image must be under 10MB.',

            'vehicle_registration_document.mimes' => 'Vehicle registration document must be jpg, jpeg, png, webp, jfif, or pdf.',
            'vehicle_registration_document.uploaded' => 'Vehicle registration document failed to upload. Please ensure it is under 5MB.',
            'vehicle_registration_document.max' => 'Vehicle registration document must be under 5MB.',

            'insurance_document.mimes' => 'Insurance document must be jpg, jpeg, png, webp, jfif, or pdf.',
            'insurance_document.uploaded' => 'Insurance document failed to upload. Please ensure it is under 5MB.',
            'insurance_document.max' => 'Insurance document must be under 5MB.',

            'road_tax_document.mimes' => 'Road tax document must be jpg, jpeg, png, webp, jfif, or pdf.',
            'road_tax_document.uploaded' => 'Road tax document failed to upload. Please ensure it is under 5MB.',
            'road_tax_document.max' => 'Road tax document must be under 5MB.',
        ]);

        $this->normalizeVehicleData($data);

        $vehicleTypeError = $this->validateVehicleTypeForWheelType($data['wheel_type'], $data['vehicle_type']);
        if ($vehicleTypeError) {
            return back()->withErrors([
                'vehicle_type' => $vehicleTypeError,
            ])->withInput();
        }

        $discountOrderError = $this->validateDurationDiscountOrder($data);
        if ($discountOrderError) {
            return back()->withErrors([
                'discount_15_days' => $discountOrderError,
            ])->withInput();
        }

        $title = trim($data['brand'] . ' ' . $data['model']) . ' (' . strtoupper(str_replace('_', ' ', $data['vehicle_type'])) . ')';

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

            'mileage_per_litre' => $data['mileage_per_litre'],
            'fuel_tank_capacity' => $data['fuel_tank_capacity'],
            'battery_capacity' => $data['battery_capacity'],
            'range_per_charge' => $data['range_per_charge'],
            'charging_time' => $data['charging_time'],
            'charger_type' => $data['charger_type'],

            'price_per_day' => $data['price_per_day'],
            'with_driver_price_per_day' => $data['with_driver_price_per_day'],

            'discount_15_days' => $data['discount_15_days'] ?? 0,
            'discount_30_days' => $data['discount_30_days'] ?? 0,
            'discount_60_days' => $data['discount_60_days'] ?? 0,

            'description' => $data['description'] ?? null,
            'location_city' => $data['location_city'],
            'insurance_expiry_date' => $data['insurance_expiry_date'],
            'road_tax_expiry_date' => $data['road_tax_expiry_date'],

            'status' => 'pending',
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
            'reject_reason' => null,
        ]);

        $vehicle->update([
            'vehicle_registration_document_path' => $request->file('vehicle_registration_document')
                ->store('vehicle-documents/registration', 'public'),
            'insurance_document_path' => $request->file('insurance_document')
                ->store('vehicle-documents/insurance', 'public'),
            'road_tax_document_path' => $request->file('road_tax_document')
                ->store('vehicle-documents/road-tax', 'public'),
            'insurance_expiry_reminder_sent_on' => null,
            'road_tax_expiry_reminder_sent_on' => null,
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

        $admins = User::query()
            ->where('role', 'admin')
            ->whereNotNull('email')
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::sendNow($admins, new VehicleApprovalRequestToAdminNotification($vehicle->fresh('vendor')));
        }

        return redirect()
            ->route('vendor.vehicles.index')
            ->with('success', 'Vehicle submitted successfully and sent for admin approval.');
    }

    public function edit(Vehicle $vehicle)
    {
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
            'wheel_type' => ['required', Rule::in(['2_wheeler', '4_wheeler'])],
            'vehicle_type' => ['required', 'string', 'max:50'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'registration_no' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vehicles', 'registration_no')->ignore($vehicle->id),
            ],
            'manufacture_year' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'fuel_type' => ['required', Rule::in(['petrol', 'diesel', 'electric'])],
            'transmission' => ['nullable', 'string', 'max:50'],
            'seating_capacity' => ['nullable', 'integer', 'min:1', 'max:12'],

            'mileage_per_litre' => ['nullable', 'numeric', 'min:0'],
            'fuel_tank_capacity' => ['nullable', 'numeric', 'min:0'],
            'battery_capacity' => ['nullable', 'numeric', 'min:0'],
            'range_per_charge' => ['nullable', 'numeric', 'min:0'],
            'charging_time' => ['nullable', 'numeric', 'min:0'],
            'charger_type' => ['nullable', 'string', 'max:100'],

            'price_per_day' => ['required', 'numeric', 'min:0'],
            'with_driver_price_per_day' => ['nullable', 'numeric', 'min:0'],

            'discount_15_days' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_30_days' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_60_days' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'location_city' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],

            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,jfif,avif', 'max:10240'],
            'vehicle_registration_document' => [
                $vehicle->vehicle_registration_document_path ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,webp,jfif,pdf',
                'max:5120',
            ],
            'insurance_document' => [
                $vehicle->insurance_document_path ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,webp,jfif,pdf',
                'max:5120',
            ],
            'insurance_expiry_date' => [
                $vehicle->insurance_expiry_date ? 'nullable' : 'required',
                'date',
                'after_or_equal:today',
            ],
            'road_tax_document' => [
                $vehicle->road_tax_document_path ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,webp,jfif,pdf',
                'max:5120',
            ],
            'road_tax_expiry_date' => [
                $vehicle->road_tax_expiry_date ? 'nullable' : 'required',
                'date',
                'after_or_equal:today',
            ],
        ], [
            'images.*.image' => 'Each vehicle image must be a valid image file.',
            'images.*.mimes' => 'Vehicle images must be jpg, jpeg, png, webp, jfif, or avif.',
            'images.*.uploaded' => 'One of the selected vehicle images failed to upload. Please reduce file size and try again.',
            'images.*.max' => 'Each vehicle image must be under 10MB.',

            'vehicle_registration_document.mimes' => 'Vehicle registration document must be jpg, jpeg, png, webp, jfif, or pdf.',
            'vehicle_registration_document.uploaded' => 'Vehicle registration document failed to upload. Please ensure it is under 5MB.',
            'vehicle_registration_document.max' => 'Vehicle registration document must be under 5MB.',

            'insurance_document.mimes' => 'Insurance document must be jpg, jpeg, png, webp, jfif, or pdf.',
            'insurance_document.uploaded' => 'Insurance document failed to upload. Please ensure it is under 5MB.',
            'insurance_document.max' => 'Insurance document must be under 5MB.',

            'road_tax_document.mimes' => 'Road tax document must be jpg, jpeg, png, webp, jfif, or pdf.',
            'road_tax_document.uploaded' => 'Road tax document failed to upload. Please ensure it is under 5MB.',
            'road_tax_document.max' => 'Road tax document must be under 5MB.',
        ]);

        $this->normalizeVehicleData($data);

        $vehicleTypeError = $this->validateVehicleTypeForWheelType($data['wheel_type'], $data['vehicle_type']);
        if ($vehicleTypeError) {
            return back()->withErrors([
                'vehicle_type' => $vehicleTypeError,
            ])->withInput();
        }

        $discountOrderError = $this->validateDurationDiscountOrder($data);
        if ($discountOrderError) {
            return back()->withErrors([
                'discount_15_days' => $discountOrderError,
            ])->withInput();
        }

        $title = trim($data['brand'] . ' ' . $data['model']) . ' (' . strtoupper(str_replace('_', ' ', $data['vehicle_type'])) . ')';

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

            'mileage_per_litre' => $data['mileage_per_litre'],
            'fuel_tank_capacity' => $data['fuel_tank_capacity'],
            'battery_capacity' => $data['battery_capacity'],
            'range_per_charge' => $data['range_per_charge'],
            'charging_time' => $data['charging_time'],
            'charger_type' => $data['charger_type'],

            'price_per_day' => $data['price_per_day'],
            'with_driver_price_per_day' => $data['with_driver_price_per_day'],

            'discount_15_days' => $data['discount_15_days'] ?? 0,
            'discount_30_days' => $data['discount_30_days'] ?? 0,
            'discount_60_days' => $data['discount_60_days'] ?? 0,

            'location_city' => $data['location_city'],
            'description' => $data['description'] ?? null,
            'insurance_expiry_date' => $data['insurance_expiry_date'] ?? $vehicle->insurance_expiry_date,
            'road_tax_expiry_date' => $data['road_tax_expiry_date'] ?? $vehicle->road_tax_expiry_date,

            'status' => 'pending',
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
            'reject_reason' => null,
        ]);

        if ($request->hasFile('vehicle_registration_document')) {
            if ($vehicle->vehicle_registration_document_path && Storage::disk('public')->exists($vehicle->vehicle_registration_document_path)) {
                Storage::disk('public')->delete($vehicle->vehicle_registration_document_path);
            }

            $vehicle->update([
                'vehicle_registration_document_path' => $request->file('vehicle_registration_document')
                    ->store('vehicle-documents/registration', 'public'),
            ]);
        }

        if ($request->hasFile('insurance_document')) {
            if ($vehicle->insurance_document_path && Storage::disk('public')->exists($vehicle->insurance_document_path)) {
                Storage::disk('public')->delete($vehicle->insurance_document_path);
            }

            $vehicle->update([
                'insurance_document_path' => $request->file('insurance_document')
                    ->store('vehicle-documents/insurance', 'public'),
            ]);
        }

        if ($request->hasFile('road_tax_document')) {
            if ($vehicle->road_tax_document_path && Storage::disk('public')->exists($vehicle->road_tax_document_path)) {
                Storage::disk('public')->delete($vehicle->road_tax_document_path);
            }

            $vehicle->update([
                'road_tax_document_path' => $request->file('road_tax_document')
                    ->store('vehicle-documents/road-tax', 'public'),
            ]);
        }

        $resetReminderPayload = [];

        if (!empty($data['insurance_expiry_date'])) {
            $resetReminderPayload['insurance_expiry_reminder_sent_on'] = null;
        }

        if (!empty($data['road_tax_expiry_date'])) {
            $resetReminderPayload['road_tax_expiry_reminder_sent_on'] = null;
        }

        if (!empty($resetReminderPayload)) {
            $vehicle->update($resetReminderPayload);
        }

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

        $admins = User::query()
            ->where('role', 'admin')
            ->whereNotNull('email')
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::sendNow($admins, new VehicleApprovalRequestToAdminNotification($vehicle->fresh('vendor')));
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

        foreach ($vehicle->images as $img) {
            if ($img->path && Storage::disk('public')->exists($img->path)) {
                Storage::disk('public')->delete($img->path);
            }
        }

        if ($vehicle->vehicle_registration_document_path && Storage::disk('public')->exists($vehicle->vehicle_registration_document_path)) {
            Storage::disk('public')->delete($vehicle->vehicle_registration_document_path);
        }

        if ($vehicle->insurance_document_path && Storage::disk('public')->exists($vehicle->insurance_document_path)) {
            Storage::disk('public')->delete($vehicle->insurance_document_path);
        }

        if ($vehicle->road_tax_document_path && Storage::disk('public')->exists($vehicle->road_tax_document_path)) {
            Storage::disk('public')->delete($vehicle->road_tax_document_path);
        }

        $vehicle->delete();

        return back()->with('success', 'Vehicle deleted successfully.');
    }

    private function normalizeVehicleData(array &$data): void
    {
        $wheelType = $data['wheel_type'] ?? null;
        $fuelType = $data['fuel_type'] ?? null;

        if ($wheelType === '2_wheeler') {
            $data['seating_capacity'] = 2;
            $data['with_driver_price_per_day'] = null;

            if (empty($data['transmission'])) {
                $data['transmission'] = $fuelType === 'electric' ? 'automatic' : 'manual';
            }
        } else {
            $data['seating_capacity'] = (int) ($data['seating_capacity'] ?? 4);
        }

        if (in_array($fuelType, ['petrol', 'diesel'])) {
            $data['battery_capacity'] = null;
            $data['range_per_charge'] = null;
            $data['charging_time'] = null;
            $data['charger_type'] = null;
        }

        if ($fuelType === 'electric') {
            $data['mileage_per_litre'] = null;
            $data['fuel_tank_capacity'] = null;
        }
    }

    private function validateVehicleTypeForWheelType(string $wheelType, string $vehicleType): ?string
    {
        $twoWheelTypes = ['bike', 'scooter'];
        $fourWheelTypes = ['car', 'suv', 'pickup', 'jeep', 'van', 'ev'];

        if ($wheelType === '2_wheeler' && ! in_array($vehicleType, $twoWheelTypes)) {
            return 'For 2 wheeler, vehicle type must be bike or scooter.';
        }

        if ($wheelType === '4_wheeler' && ! in_array($vehicleType, $fourWheelTypes)) {
            return 'For 4 wheeler, select a valid 4-wheel vehicle type.';
        }

        return null;
    }

    private function validateDurationDiscountOrder(array $data): ?string
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
