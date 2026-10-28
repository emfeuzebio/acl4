<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptInviteRequest;
use App\Models\UserInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InviteController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $invitation = UserInvitation::findValidByToken($token);

        if (! $invitation) {
            return view('auth.invite-invalid');
        }

        return view('auth.accept-invite', [
            'token' => $token,
            'user'  => $invitation->user,
        ]);
    }

    public function accept(AcceptInviteRequest $request, string $token): RedirectResponse
    {
        $invitation = UserInvitation::findValidByToken($token);

        if (! $invitation) {
            return back()->withErrors(['token' => 'Convite inválido ou expirado.']);
        }

        DB::transaction(function () use ($invitation, $request) {
            $invitation->user->update([
                'password'          => Hash::make($request->validated('password')), // cast removido: hash manual
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);

            $invitation->update(['accepted_at' => now()]);
        });

        return redirect()
            ->route('login')
            ->with('status', 'Senha definida com sucesso. Faça login.');
    }
}