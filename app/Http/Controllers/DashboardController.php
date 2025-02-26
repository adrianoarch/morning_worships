<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\User;
use App\Models\MorningWorship;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): View
    {
        $user = User::find(Auth::id());

        $totalAssistidas = $this->dashboardService->getTotalAssistidas($user);
        $totalAdoracoes = MorningWorship::count();
        $estatisticasPorMes = $this->dashboardService->getEstatisticasPorMes($user);
        $ultimasAssistidas = $this->dashboardService->getUltimasAssistidas($user);

        return view('dashboard', compact(
            'totalAssistidas',
            'totalAdoracoes',
            'estatisticasPorMes',
            'ultimasAssistidas'
        ));
    }
}
