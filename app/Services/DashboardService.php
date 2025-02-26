<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function getEstatisticasPorMes(User $user): Collection
    {
        $estatisticas = DB::table('user_watched_worships')
            ->select(
                DB::raw('DATE_FORMAT(watched_at, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('user_id', $user->id)
            ->where('watched_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes', 'desc')
            ->get();

        // Transformar a coleção em uma instância de Eloquent Collection
        return new Collection($estatisticas->toArray());
    }

    public function getUltimasAssistidas(User $user, int $limit = 5): Collection
    {
        return $user->watchedWorships()
            ->select('morning_worships.*', 'user_watched_worships.watched_at')
            ->orderBy('user_watched_worships.watched_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getTotalAssistidas(User $user): int
    {
        return $user->watchedWorships()->count();
    }
}
