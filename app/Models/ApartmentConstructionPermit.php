<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApartmentConstructionPermit extends Model
{
    use HasFactory;

    protected $table = 'apartment_construction_permits';

    protected $fillable = [
        'applicant_name',
        'national_id_or_company_registration',
        'land_plot_number',
        'location',
        'number_of_floors',
        'number_of_units',
        'approved_drawings_path',
        'engineer_or_architect_name',
        'engineer_or_architect_license',
        'permit_issue_date',
        'permit_expiry_date',
        'permit_status',
    ];

    protected $casts = [
        'permit_issue_date' => 'date',
        'permit_expiry_date' => 'date',
    ];
}
