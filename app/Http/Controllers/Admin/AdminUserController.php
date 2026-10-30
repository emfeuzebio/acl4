<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserStoreRequest;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use App\Mail\InviteUserMail;
use App\Models\System;
use App\Models\Token;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $query = User::query()
            ->whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))
            ->with(['profiles' => fn ($q) => $q->where('system_id', $systemId)]);

        $filtrosPermitidos = ['status', 'active'];
        foreach ($request->all() as $campo => $valor) {
            if (in_array($campo, $filtrosPermitidos) && $request->filled($campo)) {
                $query->where($campo, $valor);
            }
        }

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('email', 'like', $s));
        }

        $users = $query->orderBy('name')->get();

        return response()->json($users, Response::HTTP_OK);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $user = User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))
            ->with(['profiles' => fn ($q) => $q->where('system_id', $systemId)])
            ->find($id);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user, Response::HTTP_OK);
    }

    public function store(AdminUserStoreRequest $request): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        [$user, $invitation] = DB::transaction(function () use ($request, $systemId) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->filled('phone') ? $request->phone : null,
                'password' => null,
                'status'   => 'invited',
                'active'   => 'Y',
            ]);

            $user->systems()->attach($systemId);

            $invitation = UserInvitation::issueFor($user, auth()->id());

            return [$user, $invitation];
        });

        $system    = System::findOrFail($systemId);
        $acceptUrl = rtrim($system->url, '/')
            . '/#/pages/accept-invite/?token=' . $invitation->plain_token;

        $invitation->update(['accept_url' => $acceptUrl]);

        Mail::to($user->email)->send(new InviteUserMail(
            $user->name,
            $acceptUrl,
            $invitation->expires_at->diffForHumans(),
            $system->name,
            $system->mail_from_address,
            $system->mail_from_name,
        ));

        AuditService::log(
            'user.create', $systemId, $user,
            before: null,
            after: $user->only(['id', 'name', 'email', 'status', 'active']),
        );

        return response()->json(
            $user->only(['id', 'name', 'email', 'phone', 'status', 'active']),
            Response::HTTP_CREATED,
        );
    }

    public function update(AdminUserUpdateRequest $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $user = User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))->find($id);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $before = $user->only(['name', 'email', 'phone']);

        $user->update($request->only(['name', 'email', 'phone']));

        AuditService::log(
            'user.update', $systemId, $user,
            before: $before,
            after: $user->only(['name', 'email', 'phone']),
        );

        return response()->json(
            $user->only(['id', 'name', 'email', 'phone', 'status', 'active']),
            Response::HTTP_OK,
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $user = User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))->find($id);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        if ($user->id === auth('api')->id()) {
            return response()->json(['error' => 'Você não pode excluir a si próprio.'], Response::HTTP_CONFLICT);
        }

        $deps = [];

        if ($user->profiles()->count() > 0) {
            $deps[] = 'perfis de acesso';
        }
        if ($user->systems()->count() > 1) {
            $deps[] = 'outros sistemas';
        }
        if (Token::where('user_id', $user->id)->where('status', 'active')->exists()) {
            $deps[] = 'tokens ativos';
        }

        // if (UserInvitation::where('user_id', $user->id)->whereNull('accepted_at')->exists()) {
        //     $deps[] = 'convites pendentes';
        // }

        if (! empty($deps)) {
            return response()->json([
                'error'        => 'Não é possível excluir: existem dependências.',
                'dependencies' => $deps,
            ], Response::HTTP_CONFLICT);
        }

        $before = $user->only(['id', 'name', 'email', 'status', 'active']);

        DB::transaction(function () use ($user, $systemId, $before) {
            $user->systems()->detach($systemId);
            $user->delete();

            AuditService::log('user.delete', $systemId, null, before: $before, after: null);
        });

        return response()->json(['message' => 'Usuário excluído.'], Response::HTTP_OK);
    }
}