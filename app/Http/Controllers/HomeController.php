<?php

namespace App\Http\Controllers;

use App\Models\MorningWorship;
use Illuminate\Http\Request;
use Illuminate\View\View;


class HomeController extends Controller
{
    public function index(): View
    {
        $worships = MorningWorship::orderByDesc('first_published')->paginate(15);
        $watchedWorshipsCount = auth()->user()->watchedWorships->count();

        return view('worships.index', compact('worships', 'watchedWorshipsCount'));
    }

    public function toggleWatched(Request $request, MorningWorship $worship)
    {
        $user = $request->user();

        if ($worship->wasWatchedBy($user)) {
            // Se já foi assistido, remove o registro
            $user->watchedWorships()->detach($worship->id);
            $message = 'Adoração desmarcada como assistida.';
        } else {
            // Se não foi assistido, marca como assistido
            $user->watchedWorships()->attach($worship->id, [
                'watched_at' => now(),
            ]);
            $message = 'Adoração marcada como assistida!';
        }

        return back()->with('success', $message);
    }
}
