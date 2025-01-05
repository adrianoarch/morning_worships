<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\UpdateMorningWorships;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(UpdateMorningWorships::class)
    ->twiceDaily(8, 20) // Executa às 8h e 20h
    // ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground(); // Evita execuções simultâneas
    // ->appendOutputTo(storage_path('logs/scheduler.log')); // Registra logs de execução
