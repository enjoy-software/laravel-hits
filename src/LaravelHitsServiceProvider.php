<?php

namespace EnjoySoftware\LaravelHits;

use EnjoySoftware\LaravelHits\Console\Commands\CleanupHitsCommand;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class LaravelHitsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-hits.php',
            'laravel-hits'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        AboutCommand::add('Laravel Hits', fn () => ['Version' => '1.1.1']);

        $this->publishes(
            [
                __DIR__ . '/../config/laravel-hits.php' => config_path(
                    'laravel-hits.php'
                ),
            ],
            'laravel-hits-config'
        );

        $this->publishesMigrations(
            [
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ],
            'laravel-hits-migrations'
        );

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupHitsCommand::class,
            ]);
        }
    }
}
