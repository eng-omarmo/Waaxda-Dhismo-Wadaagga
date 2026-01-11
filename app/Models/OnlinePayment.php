<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlinePayment extends Model
{
    protected $fillable = [
        'pending_registration_id',
        'provider',
        'payment_method',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'reference',
        'receipt_number',
        'verified_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(PendingRegistration::class, 'pending_registration_id');
    }
}
