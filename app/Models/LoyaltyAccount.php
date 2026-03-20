<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'available_points',
        'lifetime_earned_points',
        'lifetime_redeemed_points',
        'tier',
        'completed_bookings_count',
        'yearly_spend',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
