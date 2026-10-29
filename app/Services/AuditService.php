<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;

class AuditService
{
    public static function log(
        string $action,
        int $systemId,
        ?User $targetUser = null,
        ?int $targetProfileId = null,
        ?array $before = null,
        ?array $after = null,
        ?Request $request = null,
    ): Audit {
        $request ??= request();

        return Audit::create([
            'system_id'         => $systemId,
            'actor_user_id'     => auth()->id(),
            'action'            => $action,
            'target_user_id'    => $targetUser?->id,
            'target_profile_id' => $targetProfileId,
            'payload_before'    => $before,
            'payload_after'     => $after,
            'ip'                => $request->ip(),
            'user_agent'        => substr((string) $request->header('User-Agent'), 0, 255),
            'created_at'        => now(),
        ]);
    }
}