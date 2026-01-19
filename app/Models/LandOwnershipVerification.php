<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LandOwnershipVerification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'land_parcel_id',
        'verification_request_type',
        'requested_by_admin_id',
        'applicant_national_id',
        'applicant_name',
        'status',
        'verification_method',
        'verification_result',
        'verified_by_admin_id',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'verification_result' => 'array',
        'verified_at' => 'datetime',
        'applicant_national_id' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::creating(function (LandOwnershipVerification $verification) {
            if (empty($verification->id)) {
                $verification->id = (string) Str::uuid();
            }
        });
    }

    public function landParcel(): BelongsTo
    {
        return $this->belongsTo(LandParcel::class, 'land_parcel_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_admin_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }
}
