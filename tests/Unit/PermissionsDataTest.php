<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Permissions;

it('can create permissions from array', function () {
    $data = [
        'admin' => true,
        'maintain' => false,
        'push' => true,
        'triage' => false,
        'pull' => true,
    ];

    $permissions = Permissions::fromArray($data);

    expect($permissions->admin)->toBeTrue();
    expect($permissions->maintain)->toBeFalse();
    expect($permissions->push)->toBeTrue();
    expect($permissions->triage)->toBeFalse();
    expect($permissions->pull)->toBeTrue();
});

it('can create permissions from empty array with defaults', function () {
    $permissions = Permissions::fromArray([]);

    expect($permissions->admin)->toBeFalse();
    expect($permissions->maintain)->toBeFalse();
    expect($permissions->push)->toBeFalse();
    expect($permissions->triage)->toBeFalse();
    expect($permissions->pull)->toBeFalse();
});

it('can convert permissions to array', function () {
    $data = [
        'admin' => true,
        'maintain' => true,
        'push' => false,
        'triage' => true,
        'pull' => false,
    ];

    $permissions = Permissions::fromArray($data);
    $array = $permissions->toArray();

    expect($array)->toBeArray();
    expect($array['admin'])->toBeTrue();
    expect($array['maintain'])->toBeTrue();
    expect($array['push'])->toBeFalse();
    expect($array['triage'])->toBeTrue();
    expect($array['pull'])->toBeFalse();
});
