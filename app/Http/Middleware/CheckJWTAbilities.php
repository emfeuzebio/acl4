<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckJWTAbilities
{
    public function handle(Request $request, Closure $next, ...$authorizations)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $payload = JWTAuth::parseToken()->getPayload();

            // recupera as abilidades do usuário de dentro do token
            $abilities = $payload->get('user_abilities') ?? [];
            
            // DEBUGANDO: O nome da rota atual esta na $authorizations a cada vez que o middleware é chamado
            // return response()->make(print_r($authorizations, true), 200, [
            //     'Content-Type' => 'text/plain',
            // ]);            

            // DEBUGANDO: as abilidades do usuário estão na variável $abilities
            // return response()->make(print_r($abilities, true), 200, [
            //     'Content-Type' => 'text/plain',
            // ]);            

            // Verifica se alguma das abilities exigidas está presente
            foreach ($authorizations as $ability) {
                if (!in_array($ability, $abilities)) {
                    return response()->json([
                        'error' => 'Ação não permitida: falta da ability [' . $ability . ']'
                    ], Response::HTTP_FORBIDDEN);
                }
            }            

            return $next($request);

        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao verificar autorizações'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
