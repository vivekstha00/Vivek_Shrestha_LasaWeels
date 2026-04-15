<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'driver_id',
        'service',
        'pickup_location',
        'drop_location',
        'pickup_datetime',
        'drop_datetime',
        'special_request',
        'payment_status',
        'security_deposit',
        'status',

        'original_price',
        'discount_amount',
        'discount_type',
        'discount_code',
        'total_price',

        'reminder_sent_at',
        'reminder_7d_sent_at',
        'reminder_2h_sent_at',
        'loyalty_points_earned',
        'loyalty_points_redeemed',
        'loyalty_discount_amount',
        'loyalty_processed_at',

        'cancellation_requested_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'drop_datetime' => 'datetime',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'reminder_sent_at' => 'datetime',
        'reminder_7d_sent_at' => 'datetime',
        'reminder_2h_sent_at' => 'datetime',
        'cancellation_requested_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
