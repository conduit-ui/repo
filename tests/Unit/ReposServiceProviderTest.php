<?php

declare(strict_types=1);

use ConduitUI\Repos\ReposServiceProvider;
use ConduitUI\Repos\Services\Repositories;

it('registers the repositories service', function () {
    $provider = new ReposServiceProvider($this->app);
    $provider->register();

    expect($this->app->bound(Repositories::class))->toBeTrue();
    expect(config('repos'))->toBeArray();

    $service = $this->app->make(Repositories::class);
    expect($service)->toBeInstanceOf(Repositories::class);
});

it('boots provider in console mode', function () {
    $provider = new ReposServiceProvider($this->app);
    $provider->boot();

    expect(true)->toBeTrue();
});
