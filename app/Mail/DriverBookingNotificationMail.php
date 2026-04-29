<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DriverBookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function build(): self
    {
        $booking = $this->booking->loadMissing(['user', 'vehicle', 'driver']);

        return $this->subject('New Confirmed Driver Booking - LasaWheels')
            ->view('vendor.emails.driver-booking-confirmed', compact('booking'));
    }
}
