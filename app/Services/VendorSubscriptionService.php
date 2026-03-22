<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\SubscriptionPlan;
use App\Models\Vehicle;
use App\Models\VendorSubscription;

class VendorSubscriptionService
{
    public function getActiveSubscription(int $vendorId): ?VendorSubscription
    {
        return VendorSubscription::with('plan')
            ->where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->latest('ends_at')
            ->first();
    }

    public function getCurrentPlan(int $vendorId): ?SubscriptionPlan
    {
        return $this->getActiveSubscription($vendorId)?->plan;
    }

    public function getVehicleLimit(int $vendorId): int
    {
        return $this->getCurrentPlan($vendorId)?->max_vehicles ?? 2;
    }

    public function getDriverLimit(int $vendorId): int
    {
        return $this->getCurrentPlan($vendorId)?->max_drivers ?? 2;
    }

    public function getCurrentVehicleCount(int $vendorId): int
    {
        return Vehicle::where('vendor_id', $vendorId)
            ->where('is_active', 1)
            ->where('status', '!=', 'rejected')
            ->count();
    }

    public function getCurrentDriverCount(int $vendorId): int
    {
        return Driver::where('vendor_id', $vendorId)
            ->where('status', 'approved')
            ->count();
    }

    public function canAddVehicle(int $vendorId): bool
    {
        return $this->getCurrentVehicleCount($vendorId) < $this->getVehicleLimit($vendorId);
    }

    public function canAddDriver(int $vendorId): bool
    {
        return $this->getCurrentDriverCount($vendorId) < $this->getDriverLimit($vendorId);
    }

    public function getSummary(int $vendorId): array
    {
        $subscription = $this->getActiveSubscription($vendorId);
        $plan = $subscription?->plan;

        return [
            'plan_name' => $plan?->name ?? 'Free Plan',
            'vehicle_limit' => $plan?->max_vehicles ?? 2,
            'driver_limit' => $plan?->max_drivers ?? 2,
            'vehicle_count' => $this->getCurrentVehicleCount($vendorId),
            'driver_count' => $this->getCurrentDriverCount($vendorId),
            'starts_at' => $subscription?->starts_at,
            'ends_at' => $subscription?->ends_at,
            'status' => $subscription?->status ?? 'free',
        ];
    }
}
