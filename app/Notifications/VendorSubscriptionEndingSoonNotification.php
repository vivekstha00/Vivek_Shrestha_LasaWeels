<?php

namespace App\Notifications;

use App\Models\VendorSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorSubscriptionEndingSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public VendorSubscription $vendorSubscription,
        public int $daysLeft
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subscription = $this->vendorSubscription->loadMissing('plan');

        return (new MailMessage)
            ->subject('Your Subscription Plan is Ending Soon')
            ->view('vendor.emails.subscription-ending-soon', [
                'vendorUser' => $notifiable,
                'subscription' => $subscription,
                'plan' => $subscription->plan,
                'daysLeft' => $this->daysLeft,
                'subscriptionsUrl' => route('vendor.subscriptions.index'),
            ]);
    }
}
