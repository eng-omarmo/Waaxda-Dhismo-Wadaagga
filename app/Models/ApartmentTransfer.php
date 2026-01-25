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
        'owner_profile_previous_id',
        'owner_profile_new_id',
        'approved_by_admin_id',
        'approved_at',
        'approval_reason',
        'notarized_at',
        'tax_clearance_code',
        'lien_check_status',
        'deed_pdf_path',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'approved_at' => 'datetime',
        'notarized_at' => 'datetime',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_number');
    }

    public function previousOwner()
    {
        return $this->belongsTo(OwnerProfile::class, 'owner_profile_previous_id');
    }

    public function newOwner()
    {
        return $this->belongsTo(OwnerProfile::class, 'owner_profile_new_id');
    }
}
