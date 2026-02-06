<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorVehicleController extends Controller
{
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
            'title' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['required', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'registration_no' => ['required', 'string', 'max:50', 'unique:vehicles,registration_no'],
            'fuel_type' => ['required', 'string', 'max:50'],
            'transmission' => ['required', 'string', 'max:50'],
            'seating_capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'location_city' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vehicles', 'public');
            $imageUrl = $path; // store path in image_url
        }

        Vehicle::create([
            'vendor_id' => Auth::id(),
            'title' => $data['title'],
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
            'image_url' => $imageUrl,

            // admin workflow
            'status' => 'pending',
            'is_active' => true,
            'approved_by' => null,
            'approved_at' => null,
            'reject_reason' => null,
        ]);

        return redirect()->route('vendor.vehicles.index')
            ->with('success', 'Vehicle added successfully. Waiting for admin approval.');
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
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        // If vendor edits, send to pending again (real-world workflow)
        $updatePayload = [
            'title' => $data['title'],
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

            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'reject_reason' => null,
        ];

        if ($request->hasFile('image')) {
            // delete old
            if ($vehicle->image_url && Storage::disk('public')->exists($vehicle->image_url)) {
                Storage::disk('public')->delete($vehicle->image_url);
            }
            $path = $request->file('image')->store('vehicles', 'public');
            $updatePayload['image_url'] = $path;
        }

        $vehicle->update($updatePayload);

        return redirect()->route('vendor.vehicles.index')
            ->with('success', 'Vehicle updated. Sent for admin approval again.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->vendor_id !== Auth::id()) {
            abort(403);
        }

        if ($vehicle->image_url && Storage::disk('public')->exists($vehicle->image_url)) {
            Storage::disk('public')->delete($vehicle->image_url);
        }

        $vehicle->delete();

        return back()->with('success', 'Vehicle deleted successfully.');
    }
}
