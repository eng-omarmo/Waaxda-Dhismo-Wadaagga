<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnershipHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'owner_profile_id',
        'started_at',
        'ended_at',
        'transfer_reference_number',
        'recorded_by_admin_id',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function owner()
    {
        return $this->belongsTo(OwnerProfile::class, 'owner_profile_id');
    }
}
