<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    public function awardCompletedBookingPoints(Booking $booking): void
    {
        if ($booking->status !== 'completed') {
            return;
        }

        if ($booking->payment_status !== 'paid') {
            return;
        }

        if ($booking->loyalty_processed_at) {
            return;
        }

        DB::transaction(function () use ($booking) {
            $account = LoyaltyAccount::firstOrCreate(
                ['user_id' => $booking->user_id],
                [
                    'available_points' => 0,
                    'lifetime_earned_points' => 0,
                    'lifetime_redeemed_points' => 0,
                    'tier' => 'bronze',
                    'completed_bookings_count' => 0,
                    'yearly_spend' => 0,
                ]
            );

            $eligibleAmount = max(
                0,
                (float) $booking->total_price - (float) $booking->loyalty_discount_amount
            );

            $multiplier = match ($account->tier) {
                'silver' => 1.10,
                'gold' => 1.25,
                default => 1.00,
            };

            $basePoints = (int) floor(($eligibleAmount / 100) * $multiplier);

            $isFirstCompletedBooking = $account->completed_bookings_count === 0;
            $firstBookingBonus = $isFirstCompletedBooking ? 100 : 0;

            $totalPoints = $basePoints + $firstBookingBonus;

            LoyaltyTransaction::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'event_key' => 'booking_' . $booking->id . '_earn_booking',
                'type' => 'earn_booking',
                'points' => $basePoints,
                'amount_npr' => $eligibleAmount,
                'meta' => [
                    'tier' => $account->tier,
                    'multiplier' => $multiplier,
                ],
            ]);

            if ($firstBookingBonus > 0) {
                LoyaltyTransaction::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'event_key' => 'booking_' . $booking->id . '_first_booking_bonus',
                    'type' => 'earn_first_booking_bonus',
                    'points' => $firstBookingBonus,
                    'amount_npr' => null,
                    'meta' => [
                        'reason' => 'First completed booking bonus',
                    ],
                ]);
            }

            $account->available_points += $totalPoints;
            $account->lifetime_earned_points += $totalPoints;
            $account->completed_bookings_count += 1;
            $account->yearly_spend += $eligibleAmount;
            $account->save();

            $booking->update([
                'loyalty_points_earned' => $totalPoints,
                'loyalty_processed_at' => now(),
            ]);

            $this->updateTier($booking->user->fresh());
        });
    }

    public function awardReviewBonus(Booking $booking): void
    {
        if ($booking->status !== 'completed') {
            return;
        }

        $alreadyGiven = LoyaltyTransaction::where('event_key', 'booking_' . $booking->id . '_review_bonus')->exists();

        if ($alreadyGiven) {
            return;
        }

        DB::transaction(function () use ($booking) {
            $account = LoyaltyAccount::firstOrCreate(
                ['user_id' => $booking->user_id],
                [
                    'available_points' => 0,
                    'lifetime_earned_points' => 0,
                    'lifetime_redeemed_points' => 0,
                    'tier' => 'bronze',
                    'completed_bookings_count' => 0,
                    'yearly_spend' => 0,
                ]
            );

            $bonusPoints = 25;

            LoyaltyTransaction::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'event_key' => 'booking_' . $booking->id . '_review_bonus',
                'type' => 'earn_review_bonus',
                'points' => $bonusPoints,
                'amount_npr' => null,
                'meta' => [
                    'reason' => 'Review submitted bonus',
                ],
            ]);

            $account->available_points += $bonusPoints;
            $account->lifetime_earned_points += $bonusPoints;
            $account->save();
        });
    }

    public function updateTier(User $user): void
    {
        $account = $user->loyaltyAccount;

        if (! $account) {
            return;
        }

        $newTier = 'bronze';

        if ($account->completed_bookings_count >= 10 || $account->yearly_spend >= 100000) {
            $newTier = 'gold';
        } elseif ($account->completed_bookings_count >= 5 || $account->yearly_spend >= 50000) {
            $newTier = 'silver';
        }

        if ($account->tier !== $newTier) {
            $account->update([
                'tier' => $newTier,
            ]);
        }
    }

    public function getMaxRedeemablePoints(User $user, float $subtotal): int
    {
        $account = $user->loyaltyAccount;

        if (! $account) {
            return 0;
        }

        $maxByPercent = (int) floor($subtotal * 0.15);
        $available = (int) $account->available_points;

        return min($available, $maxByPercent);
    }
}
