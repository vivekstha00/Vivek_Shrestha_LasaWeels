<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'booking_id',
        'amount',
        'method',
        'payment_type',
        'paid_amount',
        'remaining_amount',
        'deposit_amount',
        'status',
        'deposit_status',
        'settlement_status',
        'platform_commission',
        'vendor_amount',
        'payout_status',
        'gateway_reference',
        'gateway_payload',
        'paid_at',

        'refund_amount',
        'refund_status',
        'dispute_status',
        'refund_requested_at',
        'refund_processed_at',
        'refund_note',

        'paid_out_at',
        'paid_out_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'vendor_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'gateway_payload' => 'array',
        'paid_at' => 'datetime',
        'paid_out_at' => 'datetime',
        'refund_requested_at' => 'datetime',
        'refund_processed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function paidOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_out_by');
    }

    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeEligibleForPayout(Builder $query): Builder
    {
        return $query
            ->where('status', 'completed')
            ->where(function (Builder $q) {
                $q->whereNull('refund_status')
                    ->orWhere('refund_status', '!=', 'refunded');
            })
            ->where(function (Builder $q) {
                $q->whereNull('dispute_status')
                    ->orWhere('dispute_status', '!=', 'pending');
            })
            ->whereHas('booking', function (Builder $q) {
                $q->where('status', 'completed')
                    ->orWhere(function (Builder $sub) {
                        $sub->whereIn('status', ['confirmed', 'active'])
                            ->where('drop_datetime', '<=', now());
                    });
            })
            ->where(function (Builder $q) {
                $q->where('payment_type', '!=', 'deposit_cash')
                    ->orWhere('settlement_status', 'balance_received');
            });
    }

    public function isEligibleForPayout(?string $effectiveStatus = null, ?string $effectiveSettlementStatus = null): bool
    {
        $booking = $this->relationLoaded('booking')
            ? $this->booking
            : $this->booking()->select(['status', 'drop_datetime'])->first();

        if (! $booking) {
            return false;
        }

        $isBookingCompleted = $booking->status === 'completed';
        $isTripEndedWithoutAutoCompletion = in_array($booking->status, ['confirmed', 'active'], true)
            && $booking->drop_datetime
            && $booking->drop_datetime->isPast();

        if (! $isBookingCompleted && ! $isTripEndedWithoutAutoCompletion) {
            return false;
        }

        $status = $effectiveStatus ?? $this->status;
        $settlementStatus = $effectiveSettlementStatus ?? $this->settlement_status;

        if ($status !== 'completed') {
            return false;
        }

        if (($this->refund_status ?? 'none') === 'refunded') {
            return false;
        }

        if (($this->dispute_status ?? 'none') === 'pending') {
            return false;
        }

        if ($this->payment_type === 'deposit_cash' && $settlementStatus !== 'balance_received') {
            return false;
        }

        return true;
    }
}
