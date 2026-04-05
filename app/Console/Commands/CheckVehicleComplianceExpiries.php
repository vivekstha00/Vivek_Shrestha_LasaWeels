<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Notifications\VehicleComplianceReminderNotification;
use App\Notifications\VehicleDeactivatedComplianceExpiredNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckVehicleComplianceExpiries extends Command
{
    protected $signature = 'vehicles:check-compliance-expiry';

    protected $description = 'Send compliance expiry reminders and deactivate vehicles with expired insurance/road tax';

    public function handle(): int
    {
        $today = Carbon::today();
        $reminderTargetDate = $today->copy()->addDays(7)->toDateString();

        $vehiclesForReminder = Vehicle::query()
            ->with('vendor')
            ->where('status', 'approved')
            ->where(function ($query) use ($reminderTargetDate, $today) {
                $query->where(function ($q) use ($reminderTargetDate, $today) {
                    $q->whereDate('insurance_expiry_date', $reminderTargetDate)
                        ->where(function ($s) use ($today) {
                            $s->whereNull('insurance_expiry_reminder_sent_on')
                                ->orWhereDate('insurance_expiry_reminder_sent_on', '!=', $today->toDateString());
                        });
                })->orWhere(function ($q) use ($reminderTargetDate, $today) {
                    $q->whereDate('road_tax_expiry_date', $reminderTargetDate)
                        ->where(function ($s) use ($today) {
                            $s->whereNull('road_tax_expiry_reminder_sent_on')
                                ->orWhereDate('road_tax_expiry_reminder_sent_on', '!=', $today->toDateString());
                        });
                });
            })
            ->get();

        $reminderCount = 0;

        foreach ($vehiclesForReminder as $vehicle) {
            if (!$vehicle->vendor || empty($vehicle->vendor->email)) {
                continue;
            }

            if (
                $vehicle->insurance_expiry_date
                && $vehicle->insurance_expiry_date->toDateString() === $reminderTargetDate
                && (!$vehicle->insurance_expiry_reminder_sent_on || $vehicle->insurance_expiry_reminder_sent_on->toDateString() !== $today->toDateString())
            ) {
                Notification::sendNow(
                    $vehicle->vendor,
                    new VehicleComplianceReminderNotification($vehicle, 'Insurance', $vehicle->insurance_expiry_date)
                );

                $vehicle->insurance_expiry_reminder_sent_on = $today;
                $reminderCount++;
            }

            if (
                $vehicle->road_tax_expiry_date
                && $vehicle->road_tax_expiry_date->toDateString() === $reminderTargetDate
                && (!$vehicle->road_tax_expiry_reminder_sent_on || $vehicle->road_tax_expiry_reminder_sent_on->toDateString() !== $today->toDateString())
            ) {
                Notification::sendNow(
                    $vehicle->vendor,
                    new VehicleComplianceReminderNotification($vehicle, 'Road Tax', $vehicle->road_tax_expiry_date)
                );

                $vehicle->road_tax_expiry_reminder_sent_on = $today;
                $reminderCount++;
            }

            $vehicle->save();
        }

        $vehiclesToDeactivate = Vehicle::query()
            ->with('vendor')
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where(function ($query) use ($today) {
                $query->whereDate('insurance_expiry_date', '<', $today->toDateString())
                    ->orWhereDate('road_tax_expiry_date', '<', $today->toDateString());
            })
            ->get();

        $deactivatedCount = 0;

        foreach ($vehiclesToDeactivate as $vehicle) {
            $expiredItems = [];

            if ($vehicle->insurance_expiry_date && $vehicle->insurance_expiry_date->lt($today)) {
                $expiredItems[] = 'Insurance';
            }

            if ($vehicle->road_tax_expiry_date && $vehicle->road_tax_expiry_date->lt($today)) {
                $expiredItems[] = 'Road Tax';
            }

            if (empty($expiredItems)) {
                continue;
            }

            $vehicle->update([
                'is_active' => false,
            ]);

            if ($vehicle->vendor && !empty($vehicle->vendor->email)) {
                Notification::sendNow(
                    $vehicle->vendor,
                    new VehicleDeactivatedComplianceExpiredNotification($vehicle, $expiredItems)
                );
            }

            $deactivatedCount++;
        }

        $this->info("Compliance reminders sent: {$reminderCount}");
        $this->info("Vehicles deactivated due to expiry: {$deactivatedCount}");

        return self::SUCCESS;
    }
}
