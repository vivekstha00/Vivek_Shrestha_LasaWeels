<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\VendorSubscription;
use App\Notifications\VendorSubscriptionEndingSoonNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendVendorSubscriptionExpiryReminders extends Command
{
    private const FREE_PLAN_VEHICLE_LIMIT = 2;

    protected $signature = 'subscriptions:send-expiry-reminders';

    protected $description = 'Send vendor subscription expiry reminders and auto-mark expired subscriptions';

    public function handle(): int
    {
        $today = Carbon::today();
        $reminderThreshold = $today->copy()->addDays(3)->endOfDay();

        $subscriptions = VendorSubscription::query()
            ->with(['vendor', 'plan'])
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>=', $today->startOfDay())
            ->where('ends_at', '<=', $reminderThreshold)
            ->whereNull('expiry_reminder_sent_on')
            ->get();

        $reminderCount = 0;

        foreach ($subscriptions as $subscription) {
            if (!$subscription->vendor || empty($subscription->vendor->email) || !$subscription->ends_at) {
                continue;
            }

            $daysLeft = max(0, $today->diffInDays($subscription->ends_at->copy()->startOfDay(), false));

            Notification::sendNow(
                $subscription->vendor,
                new VendorSubscriptionEndingSoonNotification($subscription, $daysLeft)
            );

            $subscription->update([
                'expiry_reminder_sent_on' => $today->toDateString(),
            ]);

            $reminderCount++;
        }

        $expiringSubscriptions = VendorSubscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', $today->startOfDay())
            ->get();

        $affectedVendorIds = $expiringSubscriptions
            ->pluck('vendor_id')
            ->filter()
            ->unique()
            ->values();

        $expiredCount = 0;

        foreach ($expiringSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            $expiredCount++;
        }

        $blockedVehicleCount = 0;

        foreach ($affectedVendorIds as $vendorId) {
            $hasActivePaidSubscription = VendorSubscription::query()
                ->where('vendor_id', $vendorId)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                })
                ->exists();

            if ($hasActivePaidSubscription) {
                continue;
            }

            $activeVehicleIdsToKeep = Vehicle::query()
                ->where('vendor_id', $vendorId)
                ->where('is_active', true)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->limit(self::FREE_PLAN_VEHICLE_LIMIT)
                ->pluck('id');

            $blockedVehicleCount += Vehicle::query()
                ->where('vendor_id', $vendorId)
                ->where('is_active', true)
                ->whereNotIn('id', $activeVehicleIdsToKeep)
                ->update(['is_active' => false]);
        }

        $this->info("Subscription expiry reminders sent: {$reminderCount}");
        $this->info("Subscriptions auto-marked expired: {$expiredCount}");
        $this->info("Vehicles auto-blocked due to free plan limit: {$blockedVehicleCount}");

        return self::SUCCESS;
    }
}
