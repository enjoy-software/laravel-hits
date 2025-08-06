<?php

namespace EnjoySoftware\LaravelHits\Console\Commands;

use EnjoySoftware\LaravelHits\Models\Hit;
use Illuminate\Console\Command;

class CleanupHitsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-hits:cleanup {--days=0 : Number of days to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old hit records';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $days = $this->option('days');

        if (!is_numeric($days) || $days < 0) {
            $this->error('Days must be a non-negative number.');

            return;
        }

        $this->info("Cleaning up hits older than {$days} days...");

        $deleted = Hit::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Successfully deleted {$deleted} old hit records.");
    }
}
