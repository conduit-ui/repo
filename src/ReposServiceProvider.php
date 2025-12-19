<?php

declare(strict_types=1);

namespace ConduitUI\Repos;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\Repositories;
use Illuminate\Support\ServiceProvider;

final class ReposServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/repos.php',
            'repos'
        );

        $this->app->singleton(Repositories::class, function ($app) {
            return new Repositories($app->make(Connector::class));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/repos.php' => config_path('repos.php'),
            ], 'repos-config');
        }
    }
}
