<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    protected $fillable = [
        'service_id',
        'user_id',
        'user_full_name',
        'user_email',
        'user_phone',
        'user_national_id',
        'request_details',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'request_details' => 'array',
        'user_national_id' => 'encrypted',
        'processed_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentVerification::class);
    }
}
