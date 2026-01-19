<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessLicenseChange extends Model
{
    protected $fillable = [
        'license_id',
        'changed_by',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(BusinessLicense::class, 'license_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
