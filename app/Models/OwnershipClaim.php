<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OwnershipClaim extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'apartment_id',
        'unit_id',
        'claimant_name',
        'claimant_national_id',
        'claimant_phone',
        'claimant_email',
        'evidence_documents',
        'status',
        'reviewer_comments',
        'reviewed_by_admin_id',
        'reviewed_at',
        'last_modified_by_admin_id',
    ];

    protected $casts = [
        'evidence_documents' => 'array',
        'reviewed_at' => 'datetime',
        'claimant_national_id' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::creating(function (OwnershipClaim $claim) {
            if (empty($claim->id)) {
                $claim->id = (string) Str::uuid();
            }
        });
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }
}
