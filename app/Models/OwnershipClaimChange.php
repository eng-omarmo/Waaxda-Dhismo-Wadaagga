<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnershipClaimChange extends Model
{
    protected $fillable = [
        'claim_id',
        'changed_by',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(OwnershipClaim::class, 'claim_id', 'id');
    }
}

