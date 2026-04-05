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
        $reviewUrl = route('admin.vehicles.show', $vehicle);

        return (new MailMessage)
            ->subject('Vehicle Approval Request Submitted')
            ->view('admin.emails.vehicle-approval-request', [
                'admin' => $notifiable,
                'vehicle' => $vehicle,
                'reviewUrl' => $reviewUrl,
            ]);
    }
}
