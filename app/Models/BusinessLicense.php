<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BusinessLicense extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'company_name',
        'project_id',
        'license_type',
        'status',
        'verification_status',
        'expires_at',
        'admin_comments',
        'registrant_name',
        'registrant_email',
        'registrant_phone',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BusinessLicense $license) {
            if (empty($license->id)) {
                $license->id = (string) Str::uuid();
            }
        });
    }
}

