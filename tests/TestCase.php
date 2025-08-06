<?php

namespace EnjoySoftware\LaravelHits\Tests;

use EnjoySoftware\LaravelHits\LaravelHitsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelHitsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Configure package settings
        $app['config']->set('laravel-hits.ignore_bots', true);
        $app['config']->set('laravel-hits.cooldown_minutes', 5);
    }
}
