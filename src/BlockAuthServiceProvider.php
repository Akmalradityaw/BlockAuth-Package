<?php

namespace BlockAuth;

use Illuminate\Support\ServiceProvider;
use BlockAuth\Console\InstallCommand;

class BlockAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/blockauth'),
            ], 'blockauth-stubs');
        }
    }
}

