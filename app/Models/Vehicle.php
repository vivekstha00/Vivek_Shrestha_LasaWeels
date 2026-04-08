<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

        'fuel_tank_capacity',
        'battery_capacity',
        'range_per_charge',
        'charging_time',
        'charger_type',

        'price_per_day',
        'with_driver_price_per_day',
        'security_deposit',

        'discount_15_days',
        'discount_30_days',
        'discount_60_days',

        'location_city',
        'location_area',
        'pickup_address',

        'image_url',
        'vehicle_registration_document_path',
        'insurance_document_path',
        'insurance_expiry_date',
        'road_tax_document_path',
        'road_tax_expiry_date',
        'insurance_expiry_reminder_sent_on',
        'road_tax_expiry_reminder_sent_on',
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
        'fuel_tank_capacity' => 'decimal:2',
        'battery_capacity' => 'decimal:2',
        'range_per_charge' => 'decimal:2',
        'charging_time' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'with_driver_price_per_day' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'discount_15_days' => 'decimal:2',
        'discount_30_days' => 'decimal:2',
        'discount_60_days' => 'decimal:2',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
        'insurance_expiry_date' => 'date',
        'road_tax_expiry_date' => 'date',
        'insurance_expiry_reminder_sent_on' => 'date',
        'road_tax_expiry_reminder_sent_on' => 'date',
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

    public function getDurationDiscountPercent(int $days): float
    {
        if ($days >= 60) {
            return (float) ($this->discount_60_days ?? 0);
        }

        if ($days >= 30) {
            return (float) ($this->discount_30_days ?? 0);
        }

        if ($days >= 15) {
            return (float) ($this->discount_15_days ?? 0);
        }

        return 0;
    }
}
