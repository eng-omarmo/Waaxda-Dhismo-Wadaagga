<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandParcel extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'plot_number',
        'title_number',
        'location_district',
        'location_region',
        'size_sqm',
        'current_owner_name',
        'current_owner_national_id',
        'ownership_type',
        'verification_status',
        'verified_by_admin_id',
        'verified_at',
        'verification_documents_path',
        'verification_notes',
        'last_verification_date',
    ];

    protected $casts = [
        'size_sqm' => 'decimal:2',
        'verified_at' => 'datetime',
        'last_verification_date' => 'date',
        'verification_documents_path' => 'array',
        'current_owner_national_id' => 'encrypted',
    ];

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(LandOwnershipVerification::class, 'land_parcel_id');
    }

    public function ownershipHistories(): HasMany
    {
        return $this->hasMany(LandOwnershipHistory::class, 'land_parcel_id');
    }

    public function permits(): HasMany
    {
        return $this->hasMany(ApartmentConstructionPermit::class, 'land_plot_number', 'plot_number');
    }
}
