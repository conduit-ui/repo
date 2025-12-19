<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\License;
use ConduitUI\Repos\Data\Owner;
use ConduitUI\Repos\Data\Repository;

it('can create a repository from array', function () {
    $data = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'description' => 'A test repository',
        'visibility' => 'public',
        'default_branch' => 'main',
        'private' => false,
        'fork' => false,
        'archived' => false,
        'disabled' => false,
        'language' => 'PHP',
        'stargazers_count' => 100,
        'watchers_count' => 50,
        'forks_count' => 25,
        'open_issues_count' => 5,
        'homepage' => 'https://example.com',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
        'pushed_at' => '2023-01-03T00:00:00Z',
        'size' => 1024,
    ];

    $repository = Repository::fromArray($data);

    expect($repository->id)->toBe(1);
    expect($repository->name)->toBe('test-repo');
    expect($repository->fullName)->toBe('owner/test-repo');
    expect($repository->description)->toBe('A test repository');
    expect($repository->visibility)->toBe('public');
    expect($repository->language)->toBe('PHP');
    expect($repository->stargazersCount)->toBe(100);
});

it('can convert repository to array', function () {
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

it('can create owner from array', function () {
    $data = [
        'id' => 1,
        'login' => 'testuser',
        'type' => 'User',
        'avatar_url' => 'https://github.com/avatar.jpg',
        'html_url' => 'https://github.com/testuser',
    ];

    $owner = Owner::fromArray($data);

    expect($owner->id)->toBe(1);
    expect($owner->login)->toBe('testuser');
    expect($owner->type)->toBe('User');
});

it('can convert owner to array', function () {
    $data = [
        'id' => 2,
        'login' => 'org-name',
        'type' => 'Organization',
        'avatar_url' => 'https://github.com/org-avatar.jpg',
        'html_url' => 'https://github.com/org-name',
    ];

    $owner = Owner::fromArray($data);
    $array = $owner->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe(2);
    expect($array['login'])->toBe('org-name');
    expect($array['type'])->toBe('Organization');
    expect($array['avatar_url'])->toBe('https://github.com/org-avatar.jpg');
    expect($array['html_url'])->toBe('https://github.com/org-name');
});

it('can create license from array', function () {
    $data = [
        'key' => 'mit',
        'name' => 'MIT License',
        'spdx_id' => 'MIT',
        'url' => 'https://api.github.com/licenses/mit',
    ];

    $license = License::fromArray($data);

    expect($license->key)->toBe('mit');
    expect($license->name)->toBe('MIT License');
    expect($license->spdxId)->toBe('MIT');
});

it('can convert license to array', function () {
    $data = [
        'key' => 'apache-2.0',
        'name' => 'Apache License 2.0',
        'spdx_id' => 'Apache-2.0',
        'url' => 'https://api.github.com/licenses/apache-2.0',
    ];

    $license = License::fromArray($data);
    $array = $license->toArray();

    expect($array)->toBeArray();
    expect($array['key'])->toBe('apache-2.0');
    expect($array['name'])->toBe('Apache License 2.0');
    expect($array['spdx_id'])->toBe('Apache-2.0');
    expect($array['url'])->toBe('https://api.github.com/licenses/apache-2.0');
});
