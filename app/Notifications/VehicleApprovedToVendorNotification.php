<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleApprovedToVendorNotification extends Notification
{
    public function __construct(public Vehicle $vehicle)
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
            ->subject('Your Vehicle Was Approved')
            ->greeting('Hi ' . ($notifiable->name ?? 'Vendor') . ',')
            ->line('Great news — your vehicle has been approved and is now active.')
            ->line('Vehicle: ' . trim(($vehicle->brand ?? '') . ' ' . ($vehicle->model ?? '')))
            ->line('Registration: ' . ($vehicle->registration_no ?? 'N/A'))
            ->action('View My Vehicles', route('vendor.vehicles.index'));
    }
}
