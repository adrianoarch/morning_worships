<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\UpdateMorningWorships;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(UpdateMorningWorships::class)
    ->everyTwoHours() // Executa a cada 2 horas
    ->withoutOverlapping() // Evita execuções simultâneas
    ->runInBackground() // Executa em background
    ->appendOutputTo(storage_path('logs/scheduler.log')); // Registra logs de execução
