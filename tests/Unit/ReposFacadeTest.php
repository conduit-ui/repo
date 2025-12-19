<?php

declare(strict_types=1);

use ConduitUI\Repos\Facades\Repos;
use ConduitUI\Repos\Services\Repositories;

it('has correct facade accessor', function () {
    $reflection = new ReflectionClass(Repos::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $method->setAccessible(true);

    $accessor = $method->invoke(null);

    expect($accessor)->toBe(Repositories::class);
});
