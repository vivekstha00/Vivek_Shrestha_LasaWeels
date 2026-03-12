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
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'paid_at' => 'datetime',
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
