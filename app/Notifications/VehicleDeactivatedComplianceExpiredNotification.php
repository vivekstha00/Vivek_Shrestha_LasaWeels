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
        return (new MailMessage)
            ->subject('Vehicle Deactivated Due to Expired Compliance')
            ->greeting('Hi ' . ($notifiable->name ?? 'Vendor') . ',')
            ->line('Your vehicle has been temporarily deactivated because required compliance document(s) expired.')
            ->line('Vehicle: ' . trim(($this->vehicle->brand ?? '') . ' ' . ($this->vehicle->model ?? '')))
            ->line('Expired: ' . implode(', ', $this->expiredItems))
            ->line('Please upload updated documents. The vehicle will go through admin review again.')
            ->action('Update Vehicle Documents', route('vendor.vehicles.edit', $this->vehicle));
    }
}
