<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'refund_requested_at',
        'refund_processed_at',
        'refund_note',
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
}
