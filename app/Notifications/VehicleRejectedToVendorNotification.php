<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleRejectedToVendorNotification extends Notification
{
    public function __construct(public Vehicle $vehicle, public string $reason)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vehicle = $this->vehicle;

        return (new MailMessage)
            ->subject('Your Vehicle Was Rejected')
            ->greeting('Hi ' . ($notifiable->name ?? 'Vendor') . ',')
            ->line('Your vehicle approval request was reviewed and rejected.')
            ->line('Vehicle: ' . trim(($vehicle->brand ?? '') . ' ' . ($vehicle->model ?? '')))
            ->line('Registration: ' . ($vehicle->registration_no ?? 'N/A'))
            ->line('Reason: ' . $this->reason)
            ->line('Please update the required information/documents and submit again.')
            ->action('Edit Vehicle', route('vendor.vehicles.edit', $vehicle));
    }
}
