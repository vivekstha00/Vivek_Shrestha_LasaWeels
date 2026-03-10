<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        return view('vendor.pages.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vendor.pages.vehicles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'wheel_type' => ['required'],
            'vehicle_type' => ['required'],
            'brand' => ['required'],
            'model' => ['required'],
            'registration_no' => ['required', 'unique:vehicles,registration_no'],
            'manufacture_year' => ['required', 'integer'],
            'fuel_type' => ['required'],
            'transmission' => ['required'],
            'seating_capacity' => ['required', 'integer'],
            'mileage_per_litre' => ['nullable', 'numeric'],
            'price_per_day' => ['required', 'numeric'],
            'with_driver_price_per_day' => ['nullable', 'numeric'],
            'description' => ['nullable'],
            'location_city' => ['required', 'string', 'max:100'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
        ]);
        $title = trim($data['brand'] . ' ' . $data['model']) . ' (' . strtoupper($data['vehicle_type']) . ')';

        // 1️⃣ Create vehicle first
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
            'price_per_day' => $data['price_per_day'],
            'with_driver_price_per_day' => $data['with_driver_price_per_day'] ?? null,
            'description' => $data['description'] ?? null,
            'location_city' => $data['location_city'],

            'status' => 'available',
            'is_active' => true,
            'approved_by' => null,
            'approved_at' => null,
            'reject_reason' => null,
        ]);

        // 2️⃣ Save images
        if ($request->hasFile('images')) {

            $isFirst = true;

            foreach ($request->file('images') as $img) {

                $path = $img->store('vehicles', 'public');

                $vehicle->images()->create([
                    'path' => $path,
                    'is_primary' => $isFirst,
                ]);

                // store first image in vehicles table for thumbnail
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
            'title' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['required', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'registration_no' => ['required', 'string', 'max:50', 'unique:vehicles,registration_no,' . $vehicle->id],
            'fuel_type' => ['required', 'string', 'max:50'],
            'transmission' => ['required', 'string', 'max:50'],
            'seating_capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'location_city' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],

            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:2048'],
        ]);
        $title = trim($data['brand'] . ' ' . $data['model']) . ' (' . strtoupper($data['vehicle_type']) . ')';
        $updatePayload['title'] = $title;

        $vehicle->update([
            'title' => $title,
            'vehicle_type' => $data['vehicle_type'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'registration_no' => $data['registration_no'],
            'fuel_type' => $data['fuel_type'],
            'transmission' => $data['transmission'],
            'seating_capacity' => $data['seating_capacity'],
            'price_per_day' => $data['price_per_day'],
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

                $makePrimary = false;
                if (!$hasPrimary) {
                    $makePrimary = true;
                    $hasPrimary = true;

                    // keep image_url updated for compatibility
                    $vehicle->update(['image_url' => $path]);
                }

                $vehicle->images()->create([
                    'path' => $path,
                    'is_primary' => $makePrimary,
                ]);
            }
        }

        return redirect()->route('vendor.vehicles.index')
            ->with('success', 'Vehicle updated. Sent for admin approval again.');
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
}
