<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Branch;

it('can create a branch from array', function () {
    $data = [
        'name' => 'main',
        'protected' => true,
        'commit' => [
            'sha' => 'abc123',
            'url' => 'https://api.github.com/repos/owner/repo/commits/abc123',
        ],
    ];

    $branch = Branch::fromArray($data);

    expect($branch->name)->toBe('main');
    expect($branch->protected)->toBeTrue();
    expect($branch->commit->sha)->toBe('abc123');
});

it('can convert branch to array', function () {
    $data = [
        'name' => 'develop',
        'protected' => false,
        'commit' => [
            'sha' => 'def456',
            'url' => 'https://api.github.com/repos/owner/repo/commits/def456',
        ],
    ];

    $branch = Branch::fromArray($data);
    $array = $branch->toArray();

    expect($array)->toBeArray();
    expect($array['name'])->toBe('develop');
    expect($array['protected'])->toBeFalse();
    expect($array['commit']['sha'])->toBe('def456');
});
