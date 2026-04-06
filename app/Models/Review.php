<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'vehicle_id',
        'vendor_id',
        'driver_id',
        'overall_rating',
        'overall_review',
        'vehicle_rating',
        'vehicle_review',
        'driver_rating',
        'driver_review',
    ];

    protected $casts = [
        'overall_rating' => 'decimal:1',
        'vehicle_rating' => 'decimal:1',
        'driver_rating' => 'decimal:1',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
