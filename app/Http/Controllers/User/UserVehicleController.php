<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class UserVehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::with(['images', 'primaryImage'])
            ->where('status', 'available')
            ->where('is_active', 1)
            ->latest()
            ->paginate(9);

        return view('user.pages.vehicles.index', compact('vehicles'));
    }

    public function browseShow(Vehicle $vehicle)
    {
        $vehicle->load([
            'images',
            'primaryImage',
            'services.items',
        ]);

        $latestService = $vehicle->services->sortByDesc('service_date')->first();
        $serviceCount = $vehicle->services->count();

        $recentServiceItems = collect();

        if ($latestService) {
            $recentServiceItems = $latestService->items
                ->pluck('service_item')
                ->map(fn ($item) => ucwords(str_replace('_', ' ', $item)))
                ->values();
        }

        $maintenanceStatus = 'Well maintained';

        if ($latestService && $latestService->next_service_due_date) {
            if (now()->gt($latestService->next_service_due_date)) {
                $maintenanceStatus = 'Service due soon';
            }
        }

        return view('user.pages.vehicles.show', [
            'vehicle' => $vehicle,
            'latestService' => $latestService,
            'serviceCount' => $serviceCount,
            'recentServiceItems' => $recentServiceItems,
            'maintenanceStatus' => $maintenanceStatus,
        ]);
    }

    public function show(Request $request, Vehicle $vehicle)
    {
        $search = $request->validate([
            'service'         => ['nullable', 'in:self,driver'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'drop_location'   => ['nullable', 'string', 'max:255'],
            'pickup_datetime' => ['nullable', 'date'],
            'drop_datetime'   => ['nullable', 'date'],
        ]);

        $service = $search['service'] ?? 'self';

        $vehicle->load([
            'images',
            'primaryImage',
            'services.items',
        ]);

        $latestService = $vehicle->services->sortByDesc('service_date')->first();
        $serviceCount = $vehicle->services->count();

        $recentServiceItems = collect();

        if ($latestService) {
            $recentServiceItems = $latestService->items
                ->pluck('service_item')
                ->map(fn ($item) => ucwords(str_replace('_', ' ', $item)))
                ->values();
        }

        $maintenanceStatus = 'Well maintained';

        if ($latestService && $latestService->next_service_due_date) {
            if (now()->gt($latestService->next_service_due_date)) {
                $maintenanceStatus = 'Service due soon';
            }
        }

        return view('user.pages.vehicles.vehicle-details', [
            'vehicle' => $vehicle,
            'search' => $search,
            'service' => $service,
            'latestService' => $latestService,
            'serviceCount' => $serviceCount,
            'recentServiceItems' => $recentServiceItems,
            'maintenanceStatus' => $maintenanceStatus,
        ]);
    }
}
