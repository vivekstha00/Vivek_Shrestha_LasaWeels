<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Review;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'email',
        'phone',
        'license_number',
        'availability_status',
        'status',
        'image'
    ];

    // Relationship with Vendor (User)
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // Relationship with Bookings
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'driver_id');
    }

    public function getAverageRatingAttribute(): ?float
    {
        if (array_key_exists('reviews_avg_driver_rating', $this->attributes)) {
            $value = $this->attributes['reviews_avg_driver_rating'];
            return $value !== null ? round((float) $value, 1) : null;
        }

        $average = $this->reviews()->avg('driver_rating');

        return $average !== null ? round((float) $average, 1) : null;
    }

    public function latestBooking(): HasOne
    {
        return $this->hasOne(Booking::class)->latestOfMany('pickup_datetime');
    }
}
