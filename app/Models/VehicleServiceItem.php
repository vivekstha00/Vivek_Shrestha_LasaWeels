<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleServiceItem extends Model
{
    protected $fillable = [
        'vehicle_service_id',
        'service_item',
        'is_custom',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(VehicleService::class, 'vehicle_service_id');
    }
}
