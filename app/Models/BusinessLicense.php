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
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
            'approved_at' => 'datetime',
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

    public function documents()
    {
        return $this->hasMany(BusinessLicenseDocument::class, 'license_id', 'id');
    }

    public function changes()
    {
        return $this->hasMany(BusinessLicenseChange::class, 'license_id', 'id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        $status = strtolower((string) $this->status);

        return match ($status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-light text-dark',
        };
    }
}
