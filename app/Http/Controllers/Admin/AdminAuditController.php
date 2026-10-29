<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $systemId = $request->attributes->get('system_id');

        $logs = Audit::where('system_id', $systemId)
            ->when($request->filled('action'),         fn ($q) => $q->where('action', $request->action))
            ->when($request->filled('actor_user_id'),  fn ($q) => $q->where('actor_user_id', $request->actor_user_id))
            ->when($request->filled('target_user_id'), fn ($q) => $q->where('target_user_id', $request->target_user_id))
            ->when($request->filled('from'),           fn ($q) => $q->where('created_at', '>=', $request->from))
            ->when($request->filled('to'),             fn ($q) => $q->where('created_at', '<=', $request->to))
            ->with(['actor:id,name,email', 'targetUser:id,name,email', 'targetProfile:id,name'])
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 25));

        return response()->json($logs);
    }
}