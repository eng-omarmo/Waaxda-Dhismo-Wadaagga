<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PendingRegistration extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'status',
        'step',
        'resume_token',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(OnlinePayment::class);
    }
}
