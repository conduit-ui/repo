<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Tests;

use ConduitUI\Repos\ReposServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ReposServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Repos' => \ConduitUI\Repos\Facades\Repos::class,
        ];
    }
}
