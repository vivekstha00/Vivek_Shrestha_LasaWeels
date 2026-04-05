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
        return (new MailMessage)
            ->subject('Vehicle ' . $this->documentType . ' Expiry Reminder')
            ->greeting('Hi ' . ($notifiable->name ?? 'Vendor') . ',')
            ->line('Your vehicle ' . trim(($this->vehicle->brand ?? '') . ' ' . ($this->vehicle->model ?? '')) . ' has an upcoming compliance expiry.')
            ->line($this->documentType . ' expiry date: ' . $this->expiryDate->format('d M Y'))
            ->line('Please update and re-upload the document before expiry to avoid vehicle deactivation.')
            ->action('Manage Vehicle', route('vendor.vehicles.edit', $this->vehicle));
    }
}
