<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WatchedWorshipController;
use App\Http\Controllers\WorshipController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return redirect()->route('worships.index');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/worship/{worship}', [WorshipController::class, 'show'])->name('worship.show');
    Route::post('/worships/{worship}/summarize', [WorshipController::class, 'summarize'])->name('worships.summarize');

    Route::get('/worships', [HomeController::class, 'index'])->name('worships.index');

    Route::post('/worship/mark-as-watched/{worship}', [WatchedWorshipController::class, 'markAsWatched'])
        ->name('worship.markAsWatched');

    Route::post('/worship/mark-as-unwatched/{worship}', [WatchedWorshipController::class, 'markAsUnwatched'])
        ->name('worship.markAsUnwatched');
});



require __DIR__ . '/auth.php';
