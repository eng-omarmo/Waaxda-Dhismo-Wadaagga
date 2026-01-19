<?php

namespace App\Models;

use App\Support\StandardIdentifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'receiver_id',
        'receiver_type',
        'service_id',
        'certificate_number',
        'certificate_uid',
        'issued_at',
        'issued_by',
        'issued_to',
        'certificate_hash',
        'status',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function receiver(): MorphTo
    {
        return $this->morphTo();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public static function issueForProject(Project $project, Service $service, ?int $issuedBy = null): Certificate
    {

        $uid = (string) Str::uuid();
        $number = 'IPAMS-COC-'.date('Y').'-'.substr($project->id, 0, 8).'-'.(string) $service->id.'-'.substr($uid, 0, 8);
        $standardId = StandardIdentifier::normalize('project', $project->id);
        $hash = hash('sha256', implode('|', [
            $uid,
            $number,

            (string) $service->id,
            (string) $project->id,
            $standardId,
        ]));

        return self::create([
            'receiver_type' => Project::class,
            'receiver_id' => $project->id,
            'service_id' => $service->id,
            'certificate_number' => $number,
            'certificate_uid' => $uid,
            'issued_at' => now(),
            'issued_by' => $issuedBy,
            'issued_to' => $project->project_name,
            'certificate_hash' => $hash,
            'status' => 'draft',
            'metadata' => [
                'standardized_id' => $standardId,
            ],
        ]);
    }
}
