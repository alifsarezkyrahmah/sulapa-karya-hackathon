<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan auto-release setiap jam untuk mengecek antrean yang > 24 jam
Schedule::command('deposits:auto-release-points')->hourly();

Schedule::command('pickups:generate-daily-business')->dailyAt('06:00');