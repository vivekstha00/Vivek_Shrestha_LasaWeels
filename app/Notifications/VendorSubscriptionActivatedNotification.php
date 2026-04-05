<?php

namespace App\Notifications;

use App\Models\VendorSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorSubscriptionActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public VendorSubscription $vendorSubscription,
        public bool $isRenewal = false
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
            ->subject($this->isRenewal ? 'Subscription Renewed Successfully' : 'Subscription Plan Activated')
            ->view('vendor.emails.subscription-activated', [
                'vendorUser' => $notifiable,
                'subscription' => $subscription,
                'plan' => $subscription->plan,
                'isRenewal' => $this->isRenewal,
                'subscriptionsUrl' => route('vendor.subscriptions.index'),
            ]);
    }
}
