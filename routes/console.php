<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('queue:process-emails', function () {
    $this->info('Starting email queue processing...');
    
    Artisan::call('queue:work', [
        '--queue' => 'default',
        '--timeout' => 60,
        '--max-time' => 60,
        '--tries' => 3,
        '--stop-when-empty' => true,
    ]);
    
    $this->info('Email queue processing completed.');
})->purpose('Process email queue jobs for one minute');