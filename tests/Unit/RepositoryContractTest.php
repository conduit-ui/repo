<?php

declare(strict_types=1);

use ConduitUI\Repos\Contracts\RepositoryContract;
use ConduitUI\Repos\Contracts\RepositoryDataContract;
use ConduitUI\Repos\Services\Repositories;
use Illuminate\Support\Collection;

it('implements repository contract', function () {
    $repository = app(RepositoryContract::class);

    expect($repository)->toBeInstanceOf(RepositoryContract::class);
    expect($repository)->toBeInstanceOf(Repositories::class);
});

it('contract defines find method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('find'))->toBeTrue();

    $method = $contract->getMethod('find');
    expect($method->getNumberOfParameters())->toBe(1);
    expect($method->getReturnType()->getName())->toBe(RepositoryDataContract::class);
});

it('contract defines create method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('create'))->toBeTrue();

    $method = $contract->getMethod('create');
    expect($method->getNumberOfParameters())->toBe(1);
    expect($method->getReturnType()->getName())->toBe(RepositoryDataContract::class);
});

it('contract defines update method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('update'))->toBeTrue();

    $method = $contract->getMethod('update');
    expect($method->getNumberOfParameters())->toBe(2);
    expect($method->getReturnType()->getName())->toBe(RepositoryDataContract::class);
});

it('contract defines delete method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('delete'))->toBeTrue();

    $method = $contract->getMethod('delete');
    expect($method->getNumberOfParameters())->toBe(1);
    expect($method->getReturnType()->getName())->toBe('bool');
});

it('contract defines branches method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('branches'))->toBeTrue();

    $method = $contract->getMethod('branches');
    expect($method->getNumberOfParameters())->toBe(1);
    expect($method->getReturnType()->getName())->toBe(Collection::class);
});

it('contract defines releases method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('releases'))->toBeTrue();

    $method = $contract->getMethod('releases');
    expect($method->getNumberOfParameters())->toBe(1);
    expect($method->getReturnType()->getName())->toBe(Collection::class);
});

it('contract defines collaborators method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('collaborators'))->toBeTrue();

    $method = $contract->getMethod('collaborators');
    expect($method->getNumberOfParameters())->toBe(1);
    expect($method->getReturnType()->getName())->toBe(Collection::class);
});

it('contract defines topics method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('topics'))->toBeTrue();

    $method = $contract->getMethod('topics');
    expect($method->getNumberOfParameters())->toBe(1);
    expect($method->getReturnType()->getName())->toBe('array');
});

it('contract defines languages method', function () {
    $contract = new ReflectionClass(RepositoryContract::class);

    expect($contract->hasMethod('languages'))->toBeTrue();

    $method = $contract->getMethod('languages');
    expect($method->getNumberOfParameters())->toBe(1);
    expect($method->getReturnType()->getName())->toBe('array');
});
