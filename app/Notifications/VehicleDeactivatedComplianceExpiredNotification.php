<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleDeactivatedComplianceExpiredNotification extends Notification
{
    /** @param array<int, string> $expiredItems */
    public function __construct(public Vehicle $vehicle, public array $expiredItems)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $updateUrl = route('vendor.vehicles.edit', $this->vehicle);

        return (new MailMessage)
            ->subject('Vehicle Deactivated Due to Expired Compliance')
            ->view('vendor.emails.vehicle-compliance-deactivated', [
                'vendorUser' => $notifiable,
                'vehicle' => $this->vehicle,
                'expiredItems' => $this->expiredItems,
                'updateUrl' => $updateUrl,
            ]);
    }
}
