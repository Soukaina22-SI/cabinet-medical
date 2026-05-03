<?php
// routes/console.php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send appointment reminders every day at 08:00
Schedule::command('appointments:send-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();
