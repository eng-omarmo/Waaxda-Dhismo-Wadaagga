<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationChange extends Model
{
    protected $fillable = ['organization_id', 'changed_by', 'changes'];

    protected $casts = [
        'changes' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
