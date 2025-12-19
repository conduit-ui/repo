<?php

declare(strict_types=1);

use ConduitUI\Repos\Contracts\RepositoryDataContract;
use ConduitUI\Repos\Data\Repository;

it('repository data implements contract', function () {
    $data = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ];

    $repository = Repository::fromArray($data);

    expect($repository)->toBeInstanceOf(RepositoryDataContract::class);
});

it('contract defines fromArray method', function () {
    $contract = new ReflectionClass(RepositoryDataContract::class);

    expect($contract->hasMethod('fromArray'))->toBeTrue();

    $method = $contract->getMethod('fromArray');
    expect($method->isStatic())->toBeTrue();
    expect($method->getNumberOfParameters())->toBe(1);
});

it('contract defines toArray method', function () {
    $contract = new ReflectionClass(RepositoryDataContract::class);

    expect($contract->hasMethod('toArray'))->toBeTrue();

    $method = $contract->getMethod('toArray');
    expect($method->getReturnType()->getName())->toBe('array');
});

it('fromArray creates valid instance', function () {
    $data = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ];

    $repository = Repository::fromArray($data);

    expect($repository->id)->toBe(1);
    expect($repository->name)->toBe('test-repo');
    expect($repository->fullName)->toBe('owner/test-repo');
});

it('toArray returns array representation', function () {
    $data = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ];

    $repository = Repository::fromArray($data);
    $array = $repository->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe(1);
    expect($array['name'])->toBe('test-repo');
    expect($array['full_name'])->toBe('owner/test-repo');
});
