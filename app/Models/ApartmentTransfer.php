<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentTransfer extends Model
{
    protected $table = 'apartment_unit_transfers';

    protected $fillable = [
        'transfer_reference_number',
        'apartment_number',
        'unit_number',
        'previous_owner_name',
        'previous_owner_id',
        'new_owner_name',
        'new_owner_id',
        'transfer_reason',
        'transfer_date',
        'supporting_documents_path',
        'approval_status',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_number');
    }
}
