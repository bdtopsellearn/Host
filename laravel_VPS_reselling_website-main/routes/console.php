<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('billing:process-renewals')
    ->dailyAt('00:05')
    ->name('process-service-renewals')
    ->withoutOverlapping();

Schedule::command('services:enforce-expirations')
    ->hourly()
    ->name('enforce-expired-services')
    ->withoutOverlapping();

