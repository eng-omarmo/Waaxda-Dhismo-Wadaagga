<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentVerification extends Model
{
    protected $fillable = [
        'service_request_id',
        'amount',
        'payment_date',
        'reference_number',
        'verified_by',
        'verified_at',
        'status',
        'notes',
        'reconciled_amount',
        'reconciliation_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'reference_number' => 'encrypted',
        'verified_at' => 'datetime',
        'reconciled_amount' => 'decimal:2',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
