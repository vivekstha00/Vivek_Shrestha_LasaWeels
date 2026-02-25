<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'booking_id',
        'subject',
        'message',
        'reply_message',
        'status',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
