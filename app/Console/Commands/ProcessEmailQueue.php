<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessEmailQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:process-emails {--timeout=60}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process email queue jobs for one minute';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeout = $this->option('timeout');
        
        $this->info('Starting email queue processing...');
        
        // Process queue for the specified timeout (default 60 seconds)
        Artisan::call('queue:work', [
            '--queue' => 'default',
            '--timeout' => $timeout,
            '--max-time' => $timeout,
            '--tries' => 3,
            '--stop-when-empty' => true,
        ]);
        
        $this->info('Email queue processing completed.');
        
        return 0;
    }
}