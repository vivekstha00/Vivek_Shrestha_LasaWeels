<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',

        'title',
        'wheel_type',
        'vehicle_type',
        'brand',
        'model',
        'variant',
        'registration_no',
        'manufacture_year',

        'fuel_type',
        'transmission',
        'seating_capacity',
        'mileage_per_litre',

        'price_per_day',
        'with_driver_price_per_day',
        'security_deposit',

        'location_city',
        'location_area',
        'pickup_address',

        'image_url',
        'description',

        'status',
        'is_active',
        'approved_by',
        'approved_at',
        'reject_reason',
    ];

    protected $casts = [
        'manufacture_year' => 'integer',
        'mileage_per_litre' => 'decimal:2',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class, 'vehicle_id', 'id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(VehicleImage::class, 'vehicle_id', 'id')->where('is_primary', 1);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vehicle_id', 'id');
    }

    public function services()
    {
        return $this->hasMany(VehicleService::class)->latest('service_date');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

}

