<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_profile_id',
        'document_type',
        'document_number',
        'file_path',
        'verified_at',
        'version',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(OwnerProfile::class, 'owner_profile_id');
    }
}
