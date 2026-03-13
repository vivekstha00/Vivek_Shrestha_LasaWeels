<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCompleteBookings extends Command
{
    protected $signature = 'bookings:auto-complete';

    protected $description = 'Automatically mark confirmed/active bookings as completed when the drop datetime has passed';

    public function handle()
    {
        $now = Carbon::now();

        $updated = Booking::whereIn('status', ['confirmed', 'active'])
            ->where('drop_datetime', '<', $now)
            ->update(['status' => 'completed']);

        $this->info("Auto-completed {$updated} booking(s).");
    }
}
