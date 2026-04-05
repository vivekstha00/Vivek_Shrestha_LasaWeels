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
        $editUrl = route('vendor.vehicles.edit', $vehicle);

        return (new MailMessage)
            ->subject('Your Vehicle Was Rejected')
            ->view('vendor.emails.vehicle-rejected', [
                'vendorUser' => $notifiable,
                'vehicle' => $vehicle,
                'reason' => $this->reason,
                'editUrl' => $editUrl,
            ]);
    }
}
