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
        $vehiclesUrl = route('vendor.vehicles.index');

        return (new MailMessage)
            ->subject('Your Vehicle Was Approved')
            ->view('vendor.emails.vehicle-approved', [
                'vendorUser' => $notifiable,
                'vehicle' => $vehicle,
                'vehiclesUrl' => $vehiclesUrl,
            ]);
    }
}
