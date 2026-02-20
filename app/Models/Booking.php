<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'service',          // self | driver
        'pickup_location',
        'drop_location',
        'pickup_datetime',
        'drop_datetime',
        'status',           // pending | confirmed | completed | cancelled
        'total_price',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'drop_datetime'   => 'datetime',
        'total_price'     => 'decimal:2',
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
}
