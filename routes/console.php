<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule hourly check for vendor SLA breaches & auto-cancellations
Schedule::command('orders:check-sla-timeouts --hours=24')->hourly();
