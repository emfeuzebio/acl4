<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserQueryController extends Controller
{
    public function authorizations(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $user = User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))->find($id);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $rows = DB::table('acl_authorizations as a')
            ->join('acl_actions as ac', 'ac.id', '=', 'a.action_id')
            ->join('acl_entities as e', 'e.id', '=', 'ac.entity_id')
            ->join('acl_profiles as p', 'p.id', '=', 'a.profile_id')
            ->where('a.active', 'Y')
            ->where('p.system_id', $systemId)
            ->whereIn('p.id', $user->profiles()->pluck('acl_profiles.id'))
            ->select('e.model as entity', 'ac.action', 'ac.route', 'a.active', 'p.name as profile')
            ->orderBy('e.model')
            ->orderBy('ac.action')
            ->get();

        return response()->json($rows);
    }

    public function history(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $user = User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))->find($id);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $logs = Audit::where('system_id', $systemId)
            ->where('target_user_id', $id)
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 25));

        return response()->json($logs);
    }
}