<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'project_name',
        'location_text',
        'developer_id',
        'status',
        'registrant_name',
        'registrant_phone',
        'registrant_email',
    ];

    protected function casts(): array
    {
        return [
            'developer_id' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->id)) {
                $project->id = (string) Str::uuid();
            }
        });
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'developer_id')->withDefault();
    }
}
