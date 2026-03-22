<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'loyalty_points_earned',
        'loyalty_points_redeemed',
        'loyalty_discount_amount',
        'loyalty_processed_at',

    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'drop_datetime'   => 'datetime',
        'original_price'  => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price'     => 'decimal:2',
        'security_deposit'=> 'decimal:2',
    ];

    // Vehicle relation (vehicles.vehicle_id)
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    // User relation (users.id)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }
}
