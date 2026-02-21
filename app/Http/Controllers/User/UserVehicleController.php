<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class UserVehicleController extends Controller
{
    public function show(Request $request, Vehicle $vehicle)
    {
        // Optional: only allow viewing vehicles that are active/approved (adjust as per your logic)
        // if (!$vehicle->is_active || $vehicle->status !== 'approved') abort(404);

        // Read search params from query string (so details page keeps booking info)
        $search = $request->validate([
            'service'         => ['nullable', 'in:self,driver'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'drop_location'   => ['nullable', 'string', 'max:255'],
            'pickup_datetime' => ['nullable', 'date'],
            'drop_datetime'   => ['nullable', 'date'],
        ]);

        $service = $search['service'] ?? 'self';

        // Load images relation for multiple photos
        $vehicle->load(['images', 'primaryImage']);

        return view('user.pages.vehicle-details', [
            'vehicle' => $vehicle,
            'search'  => $search,
            'service' => $service,
        ]);
    }
}
