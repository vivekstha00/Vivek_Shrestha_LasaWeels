<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleApprovalRequestToAdminNotification extends Notification
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
            ->subject('Vehicle Approval Request Submitted')
            ->greeting('Hello Admin,')
            ->line('A vehicle has been submitted for approval review.')
            ->line('Vendor: ' . ($vehicle->vendor?->name ?? 'N/A'))
            ->line('Vehicle: ' . trim(($vehicle->brand ?? '') . ' ' . ($vehicle->model ?? '')))
            ->line('Registration: ' . ($vehicle->registration_no ?? 'N/A'))
            ->line('Please review the request in the admin panel.')
            ->action('Review Vehicle', route('admin.vehicles.show', $vehicle));
    }
}
