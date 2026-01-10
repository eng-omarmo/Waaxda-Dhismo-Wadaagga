<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'address_street',
        'contact_name',
        'contact_phone',
        'contact_email',
        'notes',
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
