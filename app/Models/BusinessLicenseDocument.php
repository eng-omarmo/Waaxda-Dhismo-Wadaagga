<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessLicenseDocument extends Model
{
    protected $fillable = [
        'license_id',
        'file_name',
        'file_path',
        'file_type',
        'document_label',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(BusinessLicense::class, 'license_id', 'id');
    }
}

