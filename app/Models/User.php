<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass-assignable attributes.
     *
     * Note: include only columns that actually exist in your users table migration.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'profile_image',
        'password',
        'role',
        'status',
        'vendor_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    /**
     * Hidden attributes for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at'        => 'datetime',
            'password'           => 'hashed',
        ];
    }

    /**
     * A user can upload many documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Admin who verified this user (if applicable).
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function vendorProfile()
    {
        return $this->hasOne(VendorProfile::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

     public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function requiredSelfDriveDocuments(): array
    {
        return ['license', 'citizenship'];
    }

    public function loyaltyAccount()
    {
        return $this->hasOne(LoyaltyAccount::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function hasApprovedSelfDriveDocuments(): bool
    {
        $requiredTypes = $this->requiredSelfDriveDocuments();

        $documents = $this->documents()
            ->whereIn('type', $requiredTypes)
            ->get()
            ->keyBy('type');

        foreach ($requiredTypes as $type) {
            if (!isset($documents[$type])) {
                return false;
            }

            if (($documents[$type]->status ?? null) !== 'approved') {
                return false;
            }
        }

        if (
            isset($documents['license']) &&
            !empty($documents['license']->expires_at) &&
            Carbon::parse($documents['license']->expires_at)->isPast()
        ) {
            return false;
        }

        return true;
    }


    public function selfDriveVerificationStatus(): string
    {
        $requiredTypes = $this->requiredSelfDriveDocuments();

        $documents = $this->documents()
            ->whereIn('type', $requiredTypes)
            ->get()
            ->keyBy('type');

        foreach ($requiredTypes as $type) {
            if (!isset($documents[$type])) {
                return 'missing';
            }
        }

        if (
            isset($documents['license']) &&
            !empty($documents['license']->expires_at) &&
            Carbon::parse($documents['license']->expires_at)->isPast()
        ) {
            return 'expired';
        }

        foreach ($requiredTypes as $type) {
            if (($documents[$type]->status ?? null) === 'rejected') {
                return 'rejected';
            }
        }

        foreach ($requiredTypes as $type) {
            if (($documents[$type]->status ?? null) !== 'approved') {
                return 'pending';
            }
        }

        return 'approved';
    }

}
