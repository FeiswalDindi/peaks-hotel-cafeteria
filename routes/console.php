<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // 🌟 Ensure this is imported

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// 🌟 THE NEW MIDNIGHT CRON JOB
Schedule::command('wallets:reset')
        ->dailyAt('00:00')
        ->timezone('Africa/Nairobi');