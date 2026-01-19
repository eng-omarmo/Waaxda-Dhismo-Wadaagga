<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandOwnershipHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'land_parcel_id',
        'owner_name',
        'owner_national_id',
        'ownership_start_date',
        'ownership_end_date',
        'transfer_reference',
        'recorded_by_admin_id',
    ];

    protected $casts = [
        'ownership_start_date' => 'date',
        'ownership_end_date' => 'date',
        'owner_national_id' => 'encrypted',
    ];

    public function landParcel(): BelongsTo
    {
        return $this->belongsTo(LandParcel::class, 'land_parcel_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_admin_id');
    }
}
