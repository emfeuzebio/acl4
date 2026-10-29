<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserProfileController extends Controller
{
    public function index(Request $request, int $id): JsonResponse
    {
        $systemId       = $request->attributes->get('system_id');
        $adminProfileId = $request->attributes->get('admin_profile_id');

        $user = $this->findUserInSystem($id, $systemId);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $grantedIds = $user->profiles()
            ->where('acl_profiles.system_id', $systemId)
            ->pluck('acl_profiles.id')
            ->all();

        $profiles = Profile::where('system_id', $systemId)
            ->where('id', '!=', $adminProfileId)
            ->where('active', 'Y')
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id'      => $p->id,
                'name'    => $p->name,
                'acronym' => $p->acronym,
                'granted' => in_array($p->id, $grantedIds, true),
            ]);

        return response()->json($profiles);
    }

    public function grant(Request $request, int $id, int $profileId): JsonResponse
    {
        $systemId       = $request->attributes->get('system_id');
        $adminProfileId = $request->attributes->get('admin_profile_id');

        $user = $this->findUserInSystem($id, $systemId);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        if ($profileId === (int) $adminProfileId) {
            return response()->json(['error' => 'O perfil administrador não pode ser concedido por este endpoint.'], 403);
        }

        $profile = Profile::where('system_id', $systemId)->where('id', $profileId)->first();

        if (! $profile) {
            return response()->json(['error' => 'Perfil não pertence a este sistema.'], 404);
        }

        if ($user->profiles()->where('acl_profiles.id', $profileId)->exists()) {
            return response()->json(['message' => 'Perfil já concedido.']);
        }

        $user->profiles()->attach($profileId);

        AuditService::log('profile.grant', $systemId, $user, targetProfileId: $profileId);

        return response()->json(['message' => 'Perfil concedido.']);
    }

    public function revoke(Request $request, int $id, int $profileId): JsonResponse
    {
        $systemId       = $request->attributes->get('system_id');
        $adminProfileId = $request->attributes->get('admin_profile_id');

        $user = $this->findUserInSystem($id, $systemId);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        if ($profileId === (int) $adminProfileId) {
            return response()->json(['error' => 'O perfil administrador não pode ser revogado por este endpoint.'], 403);
        }

        if (! $user->profiles()->where('acl_profiles.id', $profileId)->exists()) {
            return response()->json(['message' => 'Perfil não estava concedido.']);
        }

        $user->profiles()->detach($profileId);

        AuditService::log('profile.revoke', $systemId, $user, targetProfileId: $profileId);

        return response()->json(['message' => 'Perfil revogado.']);
    }

    private function findUserInSystem(int $id, int $systemId): ?User
    {
        return User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))->find($id);
    }
}