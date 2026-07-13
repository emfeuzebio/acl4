<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CheckJWTToken
{
    public function handle(Request $request, Closure $next)
    {
        // 🔧 IGNORAR VALIDAÇÃO PARA ROTA DE REFRESH
        // O refresh precisa funcionar com token expirado
        if ($request->is('api/auth/refresh') || $request->routeIs('auth.refresh')) {
            return $next($request);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (! $user) {
                return response()->json(['error' => 'Usuário não encontrado'], Response::HTTP_NOT_FOUND);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'Token não fornecido ou malformado'], Response::HTTP_UNAUTHORIZED);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token Expired'], Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid Token'], Response::HTTP_UNAUTHORIZED);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized:' . $e], Response::HTTP_UNAUTHORIZED);
        }            

        return $next($request);
    }
}