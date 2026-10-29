<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InviteUserMail;
use App\Models\System;
use App\Models\Token;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AdminUserActionController extends Controller
{
    public function activate(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');
        $user = $this->findUserInSystem($id, $systemId);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $before = $user->only(['active', 'status']);

        $user->update(['active' => 'Y']); // booted sincroniza status = active

        AuditService::log('user.activate', $systemId, $user, before: $before, after: $user->only(['active', 'status']));

        return response()->json($user->only(['id', 'active', 'status']));
    }

    public function suspend(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');
        $user = $this->findUserInSystem($id, $systemId);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        if ($user->id === auth('api')->id()) {
            return response()->json(['error' => 'Você não pode suspender a si próprio.'], 409);
        }

        $before = $user->only(['active', 'status']);

        DB::transaction(function () use ($user) {
            $user->update(['active' => 'N']); // booted sincroniza status = suspended
            Token::where('user_id', $user->id)->where('status', 'active')->update(['status' => 'revoked']);
        });

        AuditService::log('user.suspend', $systemId, $user, before: $before, after: $user->only(['active', 'status']));

        return response()->json($user->only(['id', 'active', 'status']));
    }

    public function resendInvite(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');
        $user = $this->findUserInSystem($id, $systemId);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        if ($user->status !== 'invited') {
            return response()->json(['error' => 'Usuário não está no estado de convite.'], 422);
        }

        $system = System::findOrFail($systemId);

        DB::transaction(function () use ($user) {
            UserInvitation::where('user_id', $user->id)
                ->whereNull('accepted_at')
                ->update(['expires_at' => now()]);
        });

        $invitation = UserInvitation::issueFor($user, auth('api')->id());

        $acceptUrl = rtrim($system->url, '/')
            . '/#/pages/accept-invite/?token=' . $invitation->plain_token;

        $invitation->update(['accept_url' => $acceptUrl]);

        Mail::to($user->email)->send(new InviteUserMail(
            $user->name,
            $acceptUrl,
            $invitation->expires_at->diffForHumans(),
            $system->name,
        ));

        AuditService::log('invite.resend', $systemId, $user);

        return response()->json(['message' => 'Convite reenviado.']);
    }

    public function forceReset(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');
        $user = $this->findUserInSystem($id, $systemId);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $system = System::findOrFail($systemId);
        $token  = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => Carbon::now()],
        );

        $resetUrl = rtrim($system->url, '/')
            . '/#/pages/resetpassword/?token=' . $token
            . '&email=' . urlencode($user->email);

        Mail::to($user->email)->send(new \App\Mail\ForgotPasswordMail($user->name, $resetUrl));

        AuditService::log('password.force_reset', $systemId, $user);

        return response()->json(['message' => 'E-mail de redefinição enviado.']);
    }

    private function findUserInSystem(int $id, int $systemId): ?User
    {
        return User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))->find($id);
    }
}