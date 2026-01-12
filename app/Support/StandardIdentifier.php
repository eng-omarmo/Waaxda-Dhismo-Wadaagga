<?php

namespace App\Support;

class StandardIdentifier
{
    public static function validateType(?string $type): bool
    {
        return in_array($type, ['project','license','permit','other'], true);
    }

    public static function validate(?string $type, ?string $value): bool
    {
        if (!$type) {
            return false;
        }
        if ($type === 'project') {
            return is_string($value) && preg_match('/^[0-9a-fA-F-]{36}$/', $value) === 1;
        }
        return is_string($value) && preg_match('/^[A-Za-z0-9:_\\-]{3,64}$/', $value) === 1;
    }

    public static function normalize(string $type, string $value): string
    {
        $t = strtoupper($type);
        $v = strtoupper(trim($value));
        return "IPAMS:{$t}:{$v}";
    }

    public static function compute(?string $projectId, ?string $type, ?string $value): ?string
    {
        if ($type === null || $type === 'project') {
            $id = $value ?: $projectId;
            if (!$id) {
                return null;
            }
            return self::normalize('project', $id);
        }
        if (!self::validate($type, $value)) {
            return null;
        }
        return self::normalize($type, $value);
    }

    public static function conflict(?string $projectId, ?string $type, ?string $value): bool
    {
        if ($type === 'project' && $projectId && $value && strtoupper($projectId) !== strtoupper($value)) {
            return true;
        }
        return false;
    }
}
