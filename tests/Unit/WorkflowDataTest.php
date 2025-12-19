<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Workflow;

it('can create workflow from array', function () {
    $data = [
        'id' => 1,
        'name' => 'CI',
        'path' => '.github/workflows/ci.yml',
        'state' => 'active',
        'created_at' => '2024-01-01T00:00:00Z',
        'updated_at' => '2024-01-02T00:00:00Z',
        'url' => 'https://api.github.com/repos/owner/repo/actions/workflows/1',
        'html_url' => 'https://github.com/owner/repo/actions/workflows/ci.yml',
        'badge_url' => 'https://github.com/owner/repo/workflows/CI/badge.svg',
    ];

    $workflow = Workflow::fromArray($data);

    expect($workflow->id)->toBe(1);
    expect($workflow->name)->toBe('CI');
    expect($workflow->path)->toBe('.github/workflows/ci.yml');
    expect($workflow->state)->toBe('active');
    expect($workflow->createdAt)->toBe('2024-01-01T00:00:00Z');
    expect($workflow->updatedAt)->toBe('2024-01-02T00:00:00Z');
    expect($workflow->url)->toBe('https://api.github.com/repos/owner/repo/actions/workflows/1');
    expect($workflow->htmlUrl)->toBe('https://github.com/owner/repo/actions/workflows/ci.yml');
    expect($workflow->badgeUrl)->toBe('https://github.com/owner/repo/workflows/CI/badge.svg');
});

it('can create workflow from array with minimal data', function () {
    $data = [
        'id' => 1,
        'name' => 'CI',
        'path' => '.github/workflows/ci.yml',
        'state' => 'active',
    ];

    $workflow = Workflow::fromArray($data);

    expect($workflow->id)->toBe(1);
    expect($workflow->name)->toBe('CI');
    expect($workflow->path)->toBe('.github/workflows/ci.yml');
    expect($workflow->state)->toBe('active');
    expect($workflow->createdAt)->toBeNull();
    expect($workflow->updatedAt)->toBeNull();
});

it('can convert workflow to array', function () {
    $workflow = new Workflow(
        id: 1,
        name: 'CI',
        path: '.github/workflows/ci.yml',
        state: 'active',
        createdAt: '2024-01-01T00:00:00Z',
        updatedAt: '2024-01-02T00:00:00Z',
        url: 'https://api.github.com/repos/owner/repo/actions/workflows/1',
        htmlUrl: 'https://github.com/owner/repo/actions/workflows/ci.yml',
        badgeUrl: 'https://github.com/owner/repo/workflows/CI/badge.svg',
    );

    $array = $workflow->toArray();

    expect($array)->toMatchArray([
        'id' => 1,
        'name' => 'CI',
        'path' => '.github/workflows/ci.yml',
        'state' => 'active',
        'created_at' => '2024-01-01T00:00:00Z',
        'updated_at' => '2024-01-02T00:00:00Z',
        'url' => 'https://api.github.com/repos/owner/repo/actions/workflows/1',
        'html_url' => 'https://github.com/owner/repo/actions/workflows/ci.yml',
        'badge_url' => 'https://github.com/owner/repo/workflows/CI/badge.svg',
    ]);
});
