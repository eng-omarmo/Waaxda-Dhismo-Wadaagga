<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'national_id',
        'tax_id_number',
        'contact_phone',
        'contact_email',
        'address_text',
    ];

    public function identityDocuments()
    {
        return $this->hasMany(IdentityDocument::class);
    }

    public function ownershipHistories()
    {
        return $this->hasMany(OwnershipHistory::class);
    }
}
