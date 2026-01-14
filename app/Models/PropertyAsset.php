<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'size_sqft',
        'location_text',
        'zoning',
        'legal_description_text',
        'parcel_number',
        'geo_lat',
        'geo_lng',
        'current_valuation_amount',
        'current_valuation_currency',
        'current_valuation_assessed_at',
    ];

    protected $casts = [
        'current_valuation_assessed_at' => 'datetime',
        'geo_lat' => 'float',
        'geo_lng' => 'float',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function valuations()
    {
        return $this->hasMany(PropertyValuation::class);
    }

    public function ownershipHistories()
    {
        return $this->hasMany(OwnershipHistory::class);
    }
}
