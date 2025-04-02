<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Transformers\DashboardTransformer;
use Illuminate\Http\JsonResponse;

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
        $dashboardData = $this->dashboardService->getDashboardData();
        // return response()->json(DashboardTransformer::transform($dashboardData));
        return view('home', compact('dashboardData'));
    }
}
