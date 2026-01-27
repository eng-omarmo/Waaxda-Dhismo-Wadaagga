<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngineerLicense extends Model
{
    protected $fillable = [
        'applicant_name',
        'email',
        'phone',
        'national_id',
        'engineering_field',
        'university',
        'graduation_year',
        'status',
        'admin_comments',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
