<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Notifications\BookingReminder24hNotification;
use Carbon\Carbon;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-booking-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // window: between 24h and 24h + 15min from now
        $from = Carbon::now()->addHours(24);
        $to   = Carbon::now()->addHours(24)->addMinutes(15);

        $bookings = Booking::with('user')
            ->whereNull('reminder_sent_at')
            ->whereIn('status', ['confirmed', 'approved', 'active']) 
            ->whereBetween('pickup_datetime', [$from, $to])
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            if (!$booking->user) continue;

            $booking->user->notify(new BookingReminder24hNotification($booking));

            $booking->reminder_sent_at = Carbon::now();
            $booking->save();

            $count++;
        }

        $this->info("Reminder emails sent: {$count}");
    }
}
