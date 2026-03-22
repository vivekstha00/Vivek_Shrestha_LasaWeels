<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'billing_cycle',
        'price',
        'max_vehicles',
        'max_drivers',
        'is_active',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_vehicles' => 'integer',
        'max_drivers' => 'integer',
        'is_active' => 'boolean',
    ];

    public function vendorSubscriptions(): HasMany
    {
        return $this->hasMany(VendorSubscription::class, 'subscription_plan_id');
    }
}
