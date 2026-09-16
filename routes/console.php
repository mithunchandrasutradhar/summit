<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:compute-campaign-stats')->dailyAt('02:00');
Schedule::command('app:send-registration-reminders')->dailyAt('08:00');

// Database + storage backups. `disks` in config/backup.php defaults to
// 'local' — point it at an S3-compatible off-site disk before go-live.
Schedule::command('backup:clean')->dailyAt('01:00');
Schedule::command('backup:run')->dailyAt('01:15');
Schedule::command('backup:monitor')->dailyAt('01:45');
