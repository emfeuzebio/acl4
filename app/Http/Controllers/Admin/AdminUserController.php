<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserStoreRequest;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use App\Mail\InviteUserMail;
use App\Models\System;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $users = User::query()
            ->whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('email', 'like', $s));
            })
            ->with(['profiles' => fn ($q) => $q->where('system_id', $systemId)])
            ->orderBy('name')
            ->paginate($request->integer('per_page', 25));

        return response()->json($users);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $user = User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))
            ->with(['profiles' => fn ($q) => $q->where('system_id', $systemId)])
            ->find($id);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        return response()->json($user);
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
        ));

        AuditService::log(
            'user.create', $systemId, $user,
            before: null,
            after: $user->only(['id', 'name', 'email', 'status', 'active']),
        );

        return response()->json($user->only(['id', 'name', 'email', 'phone', 'status', 'active']), 201);
    }

    public function update(AdminUserUpdateRequest $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $user = User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))->find($id);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $before = $user->only(['name', 'email', 'phone']);

        $user->update($request->only(['name', 'email', 'phone']));

        AuditService::log(
            'user.update', $systemId, $user,
            before: $before,
            after: $user->only(['name', 'email', 'phone']),
        );

        return response()->json($user->only(['id', 'name', 'email', 'phone', 'status', 'active']));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $user = User::whereHas('systems', fn ($q) => $q->where('acl_systems.id', $systemId))->find($id);

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        if ($user->id === auth('api')->id()) {
            return response()->json(['error' => 'Você não pode excluir a si próprio.'], 409);
        }

        $deps = [];

        if ($user->profiles()->count() > 0)                $deps[] = 'perfis de acesso';
        if ($user->systems()->count() > 1)                 $deps[] = 'outros sistemas';
        if (\App\Models\Token::where('user_id', $user->id)->where('status', 'active')->exists()) $deps[] = 'tokens ativos';
        if (UserInvitation::where('user_id', $user->id)->whereNull('accepted_at')->exists())     $deps[] = 'convites pendentes';

        if (! empty($deps)) {
            return response()->json([
                'error'        => 'Não é possível excluir: existem dependências.',
                'dependencies' => $deps,
            ], 409);
        }

        $before = $user->only(['id', 'name', 'email', 'status', 'active']);

        DB::transaction(function () use ($user, $systemId, $before) {
            $user->systems()->detach($systemId);
            $user->delete();

            AuditService::log('user.delete', $systemId, null, before: $before, after: null);
        });

        return response()->json(['message' => 'Usuário excluído.']);
    }
}