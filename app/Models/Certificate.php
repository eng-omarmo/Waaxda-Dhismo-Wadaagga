<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Support\StandardIdentifier;

class Certificate extends Model
{
    protected $fillable = [
        'project_id',
        'service_id',
        'certificate_number',
        'certificate_uid',
        'issued_at',
        'issued_by',
        'certificate_hash',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public static function issueForProject(Project $project, ?Service $service, ?int $issuedBy = null): Certificate
    {
        $existing = self::where('project_id', $project->id)
            ->where('service_id', optional($service)->id)
            ->first();
        if ($existing) {
            return $existing;
        }
        $uid = (string) Str::uuid();
        $number = 'IPAMS-COC-'.date('Y').'-'.substr($project->id, 0, 8).'-'.(string) optional($service)->id;
        $standardId = StandardIdentifier::normalize('project', $project->id);
        $hash = hash('sha256', implode('|', [
            $uid,
            $number,
            $project->id,
            (string) optional($service)->id,
            $project->created_at?->toDateTimeString() ?? '',
            $standardId,
        ]));
        return self::create([
            'project_id' => $project->id,
            'service_id' => optional($service)->id,
            'certificate_number' => $number,
            'certificate_uid' => $uid,
            'issued_at' => now(),
            'issued_by' => $issuedBy,
            'certificate_hash' => $hash,
            'metadata' => [
                'design_version' => 'v1',
                'format' => 'clearance',
                'standardized_id' => $standardId,
            ],
        ]);
    }
}
