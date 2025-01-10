<?php

namespace App\Http\Controllers;

use App\Models\MorningWorship;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorshipController extends Controller
{
    public function show(MorningWorship $worship) : View
    {
        return view('worships.show', compact('worship'));
    }
}
