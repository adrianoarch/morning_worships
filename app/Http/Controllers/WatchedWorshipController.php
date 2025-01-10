<?php

namespace App\Http\Controllers;

use App\Models\MorningWorship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchedWorshipController extends Controller
{
    // Marcar uma adoração como assistida
    public function markAsWatched($worshipId)
    {
        $worship = MorningWorship::findOrFail($worshipId);
        $user = Auth::user();
        $user->watchedWorships()->syncWithoutDetaching([$worshipId => ['watched_at' => now()]]);

        return response()->json([
            'success' => true,
            'action' => 'marked',
            'watchedCount' => $user->watchedWorships()->count() // Retorna o contador atualizado
        ]);
    }

    // Desmarcar uma adoração como assistida
    public function markAsUnwatched($worshipId)
    {
        $worship = MorningWorship::findOrFail($worshipId);
        $user = Auth::user();
        $user->watchedWorships()->detach($worshipId);

        return response()->json([
            'success' => true,
            'action' => 'unmarked',
            'watchedCount' => $user->watchedWorships()->count() // Retorna o contador atualizado
        ]);
    }
}
