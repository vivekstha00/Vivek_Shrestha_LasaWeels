<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * Note: include only columns that actually exist in your documents table migration.
     */
    protected $fillable = [
        'user_id',
        'type',
        'document_number',
        'issued_at',
        'expires_at',
        'file_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * The owner of the document (user/vendor).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin who reviewed the document (optional).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
