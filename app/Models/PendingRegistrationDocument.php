<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingRegistrationDocument extends Model
{
    protected $fillable = [
        'pending_registration_id',
        'file_name',
        'file_path',
        'file_type',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(PendingRegistration::class, 'pending_registration_id');
    }
}
