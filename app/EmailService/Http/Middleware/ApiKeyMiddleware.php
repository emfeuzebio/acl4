<?php

namespace App\EmailService\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Api-Key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'error'   => 'API key ausente',
            ], 401);
        }

        $keys = config('email_service.keys', []);
        $matched = null;
        $consumerId = null;

        foreach ($keys as $id => $config) {
            $storedKey = $config['key'] ?? null;

            if (!empty($storedKey) && hash_equals($storedKey, $apiKey)) {
                $matched = $config;
                $consumerId = $id;
                break;
            }
        }

        if (!$matched) {
            Log::warning('EmailService: chave inválida', [
                'ip'  => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'API key inválida',
            ], 401);
        }

        $request->attributes->set('consumer_id', $consumerId);
        $request->attributes->set('consumer_config', $matched);

        return $next($request);
    }
}