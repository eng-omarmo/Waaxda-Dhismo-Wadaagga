<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PendingRegistration extends Model
{
    protected $fillable = [
        'service_id',
        'service_slug',
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
