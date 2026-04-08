<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'phone',
        'license_number',
        'availability_status',
        'rating',
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

    public function latestBooking(): HasOne
    {
        return $this->hasOne(Booking::class)->latestOfMany('pickup_datetime');
    }
}
