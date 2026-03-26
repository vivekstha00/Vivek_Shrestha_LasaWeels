<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'national_id_number',
        'residential_address',
        'business_name',
        'business_type',
        'business_registration_number',
        'tax_id_number',
        'business_address',
        'latitude',
        'longitude',
        'current_step',
        'is_submitted',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'reviewed_at'  => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
