<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptInviteRequest;
use App\Models\UserInvitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InviteApiController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $invitation = UserInvitation::findValidByToken($token);

        if (! $invitation) {
            return response()->json(['error' => 'Convite inválido ou expirado.'], 404);
        }

        return response()->json([
            'name'       => $invitation->user->name,
            'email'      => $invitation->user->email,
            'expires_at' => $invitation->expires_at,
        ]);
    }

    public function accept(AcceptInviteRequest $request, string $token): JsonResponse
    {
        $invitation = UserInvitation::findValidByToken($token);

        if (! $invitation) {
            return response()->json(['error' => 'Convite inválido ou expirado.'], 404);
        }

        DB::transaction(function () use ($invitation, $request) {
            $invitation->user->update([
                'password'          => Hash::make($request->validated('password')),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);

            $invitation->update(['accepted_at' => now()]);
        });

        return response()->json(['message' => 'Senha definida com sucesso. Faça login.'], 200);
    }
}