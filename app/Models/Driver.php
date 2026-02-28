<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // Relationship with Bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
