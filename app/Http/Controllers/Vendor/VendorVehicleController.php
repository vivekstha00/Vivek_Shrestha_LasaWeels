<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorVehicleController extends Controller
{
    /**
     * Show all vehicles added by the logged-in vendor
     */
    public function index()
    {
        $vehicles = Vehicle::where('vendor_id', Auth::id())
            ->latest()
            ->get();

        return view('vendor.pages.vehicles.index', compact('vehicles'));
    }

    /**
     * Show add vehicle form
     */
    public function create()
    {
        return view('vendor.pages.vehicles.create');
    }

    /**
     * Store new vehicle (status = pending, admin will approve)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'vehicle_type'     => 'required|string',
            'brand'            => 'required|string',
            'model'            => 'required|string',
            'registration_no'  => 'required|string|unique:vehicles,registration_no',
            'fuel_type'        => 'required|string',
            'transmission'     => 'required|string',
            'price_per_day'    => 'required|numeric',
            'location_city'    => 'required|string',
        ]);

        Vehicle::create([
            'vendor_id'        => Auth::id(),

            'title'            => $request->title,
            'vehicle_type'     => $request->vehicle_type,
            'brand'            => $request->brand,
            'model'            => $request->model,
            'registration_no'  => $request->registration_no,
            'fuel_type'        => $request->fuel_type,
            'transmission'     => $request->transmission,
            'price_per_day'    => $request->price_per_day,
            'location_city'    => $request->location_city,

            // important defaults
            'status'           => 'pending',
            'is_active'        => true,
        ]);

        return redirect()
            ->route('vendor.vehicles.index')
            ->with('success', 'Vehicle added successfully. Waiting for admin approval.');
    }
}
