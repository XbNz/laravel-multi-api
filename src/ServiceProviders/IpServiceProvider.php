<?php

declare(strict_types=1);

namespace XbNz\Resolver\ServiceProviders;

class IpServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/ip-resolver.php', 'ip-resolver');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/ip-resolver.php' =>
                    config_path('ip-resolver.php'),
            ], 'ip-resolver');
        }
    }
}
