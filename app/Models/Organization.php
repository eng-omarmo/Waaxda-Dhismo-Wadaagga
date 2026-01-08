<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'registration_number',
        'address',
        'type',
        'contact_full_name',
        'contact_role',
        'contact_phone',
        'contact_email',
        'status',
        'admin_notes',
    ];
}

