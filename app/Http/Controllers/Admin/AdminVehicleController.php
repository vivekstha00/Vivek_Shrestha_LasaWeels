<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VehicleApprovedToVendorNotification;
use App\Notifications\VehicleRejectedToVendorNotification;

class AdminVehicleController extends Controller
{
    // List vehicles (pending first)
    public function index(Request $request)
    {
        $status = $request->query('status'); // optional filter

        $vehicles = Vehicle::with('vendor')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByRaw("FIELD(status,'pending','rejected','approved')")
            ->latest()
            ->paginate(10);

        return view('admin.vehicle.index', compact('vehicles', 'status'));
    }

    // Approve a vehicle
    public function approve(Vehicle $vehicle)
    {
        if (! $vehicle->hasValidCompliance()) {
            return back()->with(
                'error',
                'This vehicle cannot be approved until its compliance documents are renewed: '
                    . implode(', ', $vehicle->complianceIssues()) . '.'
            );
        }

        $vehicle->update([
            'status'       => 'approved',
            'approved_by'  => Auth::id(),
            'approved_at'  => now(),
            'reject_reason'=> null,
            'is_active'    => true,
        ]);

        if ($vehicle->vendor && !empty($vehicle->vendor->email)) {
            Notification::sendNow($vehicle->vendor, new VehicleApprovedToVendorNotification($vehicle->fresh('vendor')));
        }

        return back()->with('success', 'Vehicle approved successfully.');
    }

    // Reject a vehicle
    public function reject(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'reject_reason' => ['required', 'string', 'max:500'],
        ]);

        $vehicle->update([
            'status'        => 'rejected',
            'approved_by'   => Auth::id(),
            'approved_at'   => now(),
            'reject_reason' => $data['reject_reason'],
            'is_active'     => false,
        ]);

        if ($vehicle->vendor && !empty($vehicle->vendor->email)) {
            Notification::sendNow($vehicle->vendor, new VehicleRejectedToVendorNotification($vehicle->fresh('vendor'), $data['reject_reason']));
        }

        return back()->with('success', 'Vehicle rejected successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['images', 'vendor', 'bookings']);

        return view('admin.vehicle.show', compact('vehicle'));
    }

    public function toggleActive(Vehicle $vehicle)
    {
        if ($vehicle->is_active) {
            $vehicle->update(['is_active' => false]);

            return back()->with('success', 'Vehicle deactivated successfully.');
        }

        if ($vehicle->status !== 'approved') {
            return back()->with('error', 'Only approved vehicles can be activated.');
        }

        if (! $vehicle->hasValidCompliance()) {
            return back()->with(
                'error',
                'This vehicle cannot be activated until its compliance documents are renewed: '
                    . implode(', ', $vehicle->complianceIssues()) . '.'
            );
        }

        $vehicle->update(['is_active' => true]);

        return back()->with('success', 'Vehicle activated successfully.');
    }
}
