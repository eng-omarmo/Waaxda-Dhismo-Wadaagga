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

    public static function issueForProject(?Service $service, ?int $issuedBy = null): Certificate
    {

        $uid = (string) Str::uuid();
        $number = 'IPAMS-COC-'.date('Y').'-'.substr($uid, 0, 8).'-'.(string) optional($service)->id.'-'.substr($uid, 0, 8);
        $standardId = StandardIdentifier::normalize('project', $uid);
        $hash = hash('sha256', implode('|', [
            $uid,
            $number,

            (string) optional($service)->id,
            $uid,
            $standardId,
        ]));

        return self::create([
            'receiver_type' => $service->name,
            'receiver_id' => $uid,
            'service_id' => optional($service)->id,
            'certificate_number' => $number,
            'certificate_uid' => $uid,
            'issued_at' => now(),
            'issued_by' => $issuedBy,
            'issued_to' => $service->name,
            'certificate_hash' => $hash,
            'status' => 'draft',
            'metadata' => [
                'standardized_id' => $standardId,
            ],
        ]);
    }
}
