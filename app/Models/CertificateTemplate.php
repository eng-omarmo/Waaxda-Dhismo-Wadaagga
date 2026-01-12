<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'service_id',
        'template_slug',
        'template_name',
        'variables_schema',
        'branding',
        'html_template',
        'format_options',
        'active',
    ];

    protected $casts = [
        'variables_schema' => 'array',
        'branding' => 'array',
        'format_options' => 'array',
        'active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
