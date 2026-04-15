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
    protected $description = 'Send booking reminder emails for 1 week, 24 hours, and 2 hours before pickup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $windowMinutes = 15;
        $validStatuses = ['confirmed', 'approved', 'active'];

        $reminderConfigs = [
            [
                'hours' => 24 * 7,
                'column' => 'reminder_7d_sent_at',
                'label' => '1 week',
            ],
            [
                'hours' => 24,
                'column' => 'reminder_sent_at',
                'label' => '24 hours',
            ],
            [
                'hours' => 2,
                'column' => 'reminder_2h_sent_at',
                'label' => '2 hours',
            ],
        ];

        $totalSent = 0;

        foreach ($reminderConfigs as $config) {
            $from = $now->copy()->addHours($config['hours']);
            $to = $from->copy()->addMinutes($windowMinutes);

            $bookings = Booking::with('user')
                ->whereNull($config['column'])
                ->whereIn('status', $validStatuses)
                ->whereBetween('pickup_datetime', [$from, $to])
                ->get();

            $sentForWindow = 0;

            foreach ($bookings as $booking) {
                if (! $booking->user) {
                    continue;
                }

                $booking->user->notify(new BookingReminder24hNotification($booking, $config['label']));

                $booking->{$config['column']} = $now;
                $booking->save();

                $sentForWindow++;
                $totalSent++;
            }

            $this->info("{$config['label']} reminders sent: {$sentForWindow}");
        }

        $this->info("Total reminder emails sent: {$totalSent}");
    }
}
