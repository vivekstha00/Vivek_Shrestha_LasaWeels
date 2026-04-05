<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class UserVehicleController extends Controller
{
    private function applyPublicVisibilityFilters($query)
    {
        return $query
            ->where('status', 'approved')
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('insurance_expiry_date')
                    ->orWhereDate('insurance_expiry_date', '>=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('road_tax_expiry_date')
                    ->orWhereDate('road_tax_expiry_date', '>=', now()->toDateString());
            });
    }

    public function index(Request $request)
    {
        $query = $this->applyPublicVisibilityFilters(
            Vehicle::with(['images', 'primaryImage'])
        );

        if ($request->filled('wheel_type')) {
            $query->where('wheel_type', $request->wheel_type);
        }

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        $vehicles = $query->latest()->paginate(9)->withQueryString();

        return view('user.pages.vehicles.index', compact('vehicles'));
    }

    public function browseShow(Vehicle $vehicle)
    {
        abort_unless(
            $vehicle->status === 'approved'
            && (bool) $vehicle->is_active
            && (is_null($vehicle->insurance_expiry_date) || $vehicle->insurance_expiry_date->isToday() || $vehicle->insurance_expiry_date->isFuture()),
            404
        );

        abort_unless(
            is_null($vehicle->road_tax_expiry_date) || $vehicle->road_tax_expiry_date->isToday() || $vehicle->road_tax_expiry_date->isFuture(),
            404
        );

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
        abort_unless(
            $vehicle->status === 'approved'
            && (bool) $vehicle->is_active
            && (is_null($vehicle->insurance_expiry_date) || $vehicle->insurance_expiry_date->isToday() || $vehicle->insurance_expiry_date->isFuture()),
            404
        );

        abort_unless(
            is_null($vehicle->road_tax_expiry_date) || $vehicle->road_tax_expiry_date->isToday() || $vehicle->road_tax_expiry_date->isFuture(),
            404
        );

        $search = $request->validate([
            'service'         => ['nullable', 'in:self,driver'],
            'wheel_type'      => ['nullable', 'in:2_wheeler,4_wheeler'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'drop_location'   => ['nullable', 'string', 'max:255'],
            'pickup_datetime' => ['nullable', 'date'],
            'drop_datetime'   => ['nullable', 'date'],
        ]);

        $service = $search['service'] ?? 'self';

        if ($vehicle->wheel_type === '2_wheeler') {
            $service = 'self';
            $search['service'] = 'self';
            $search['wheel_type'] = '2_wheeler';
        }

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
