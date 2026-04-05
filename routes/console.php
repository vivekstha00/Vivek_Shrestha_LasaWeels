<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {to? : Recipient email address}', function (?string $to = null) {
    $recipient = $to ?: config('mail.from.address');

    if (!$recipient) {
        $this->error('No recipient email found. Pass one explicitly: php artisan mail:test your@email.com');
        return self::FAILURE;
    }

    $this->info('Sending test email...');
    $this->line('Mailer: ' . config('mail.default'));
    $this->line('SMTP Host: ' . config('mail.mailers.smtp.host'));
    $this->line('SMTP Port: ' . config('mail.mailers.smtp.port'));
    $this->line('To: ' . $recipient);

    try {
        Mail::raw('This is a test email from LasaWheels (' . now()->toDateTimeString() . ').', function ($message) use ($recipient) {
            $message->to($recipient)
                ->subject('LasaWheels SMTP Test');
        });

        $this->info('✅ Mail send call completed without exception. Check recipient inbox/spam.');
        return self::SUCCESS;
    } catch (\Throwable $e) {
        $this->error('❌ Mail send failed: ' . $e->getMessage());
        return self::FAILURE;
    }
})->purpose('Send a test email to verify SMTP configuration');

Schedule::command('bookings:auto-complete')->everyFiveMinutes();
Schedule::command('app:send-booking-reminders')->everyFifteenMinutes();
Schedule::command('vehicles:check-compliance-expiry')->dailyAt('08:00');
Schedule::command('subscriptions:send-expiry-reminders')->dailyAt('09:00');
