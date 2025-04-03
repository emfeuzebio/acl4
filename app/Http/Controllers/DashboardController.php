<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Transformers\DashboardTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
    }

    // public function index(): JsonResponse
    public function index()
    {
        // return response()->json(DashboardTransformer::transform($dashboardData));
        $dashboardData = $this->dashboardService->getDashboardData();
        return view('home', compact('dashboardData'));
    }

    public function listLogins()
    {
        // TODO - enviar para Service com os de cima

        // Tempo limite para considerar um usuário ativo (exemplo: 15 minutos)
        $activeThreshold = Carbon::now()->subMinutes(15)->timestamp;

        // Busca os usuários ativos na tabela de sessões
        $activeUsers = DB::table('sessions')
        ->where('last_activity', '>=', $activeThreshold)        // Apenas sessões recentes
        ->whereNotNull('user_id')                               // Apenas usuários autenticados
        ->join('users', 'sessions.user_id', '=', 'users.id')    // Relaciona com os dados do usuário
        ->select(
            'users.id',
            'users.name',
            'sessions.ip_address',
            'sessions.user_agent',
            DB::raw("DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(sessions.last_activity), '+00:00', '-03:00'), '%d/%m/%Y %H:%i:%s') as last_activity")                
        )
        ->orderBy('sessions.last_activity', 'desc')
        ->get();

        return response()->json($activeUsers);
    }

    public function logoutUser(Request $request): JsonResponse
    {
        $session = DB::table('sessions')->where('user_id', $request->id)->first();
    
        if (!$session) {
            return response()->json(['message' => 'Usuário não está logado.'], Response::HTTP_BAD_REQUEST);   // 400
        }
    
        // Deleta a sessão do usuário (forçando o logout)
        DB::table('sessions')->where('user_id', $request->id)->delete();
    
        return response()->json(['message' => 'Usuário deslogado com sucesso!'], Response::HTTP_OK); 
    }    
}
