<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'apartment_id',
        'unit_number',
        'unit_type',
        'square_footage',
        'monthly_rent',
        'status',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
