<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Carbon\CarbonInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleComplianceReminderNotification extends Notification
{
    public function __construct(
        public Vehicle $vehicle,
        public string $documentType,
        public CarbonInterface $expiryDate
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $manageUrl = route('vendor.vehicles.edit', $this->vehicle);

        return (new MailMessage)
            ->subject('Vehicle ' . $this->documentType . ' Expiry Reminder')
            ->view('vendor.emails.vehicle-compliance-reminder', [
                'vendorUser' => $notifiable,
                'vehicle' => $this->vehicle,
                'documentType' => $this->documentType,
                'expiryDate' => $this->expiryDate,
                'manageUrl' => $manageUrl,
            ]);
    }
}
