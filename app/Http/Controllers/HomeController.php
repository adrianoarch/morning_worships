<?php

namespace App\Http\Controllers;

use App\Models\MorningWorship;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $worships = MorningWorship::orderBy('first_published', 'desc')
            ->paginate(15);

        return view('worships.index', compact('worships'));
    }

    public function markAsWatched($id)
    {
        $worship = MorningWorship::findOrFail($id);
        $worship->update(['watched_at' => now()]);

        return back()->with('success', 'Adoração marcada como assistida!');
    }
}
