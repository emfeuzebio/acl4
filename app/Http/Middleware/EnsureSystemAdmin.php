<?php

namespace App\Http\Middleware;

use App\Models\System;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSystemAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $payload = auth('api')->payload();
        $systemId = $payload->get('system_id');

        if (! $systemId) {
            return response()->json(['error' => 'Token sem system_id.'], 403);
        }

        $system = System::find($systemId);

        if (! $system || $system->user_management_enabled !== 'Y') {
            return response()->json(['error' => 'Gestão de usuários desabilitada para este sistema.'], 403);
        }

        if (! $system->admin_profile_id) {
            return response()->json(['error' => 'Sistema sem perfil administrador configurado.'], 403);
        }

        $isAdmin = $request->user('api')
            ->profiles()
            ->where('acl_profiles.id', $system->admin_profile_id)
            ->where('acl_profiles.active', 'Y')
            ->exists();

        if (! $isAdmin) {
            return response()->json(['error' => 'Acesso negado: perfil administrador requerido.'], 403);
        }

        // injeta contexto para os controllers
        $request->attributes->set('system_id', $systemId);
        $request->attributes->set('admin_profile_id', $system->admin_profile_id);

        return $next($request);
    }
}