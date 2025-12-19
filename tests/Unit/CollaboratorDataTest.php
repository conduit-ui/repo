<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Collaborator;

it('can create collaborator from array', function () {
    $data = [
        'id' => 1,
        'login' => 'john-doe',
        'avatar_url' => 'https://github.com/avatars/john.jpg',
        'html_url' => 'https://github.com/john-doe',
        'permissions' => [
            'admin' => true,
            'maintain' => true,
            'push' => true,
            'triage' => false,
            'pull' => true,
        ],
    ];

    $collaborator = Collaborator::fromArray($data);

    expect($collaborator->id)->toBe(1);
    expect($collaborator->login)->toBe('john-doe');
    expect($collaborator->avatarUrl)->toBe('https://github.com/avatars/john.jpg');
    expect($collaborator->htmlUrl)->toBe('https://github.com/john-doe');
    expect($collaborator->permissions->admin)->toBeTrue();
    expect($collaborator->permissions->push)->toBeTrue();
});

it('can create collaborator without permissions', function () {
    $data = [
        'id' => 2,
        'login' => 'jane-smith',
        'avatar_url' => 'https://github.com/avatars/jane.jpg',
        'html_url' => 'https://github.com/jane-smith',
    ];

    $collaborator = Collaborator::fromArray($data);

    expect($collaborator->permissions->admin)->toBeFalse();
    expect($collaborator->permissions->maintain)->toBeFalse();
    expect($collaborator->permissions->push)->toBeFalse();
    expect($collaborator->permissions->triage)->toBeFalse();
    expect($collaborator->permissions->pull)->toBeFalse();
});

it('can convert collaborator to array', function () {
    $data = [
        'id' => 3,
        'login' => 'bob-wilson',
        'avatar_url' => 'https://github.com/avatars/bob.jpg',
        'html_url' => 'https://github.com/bob-wilson',
        'permissions' => [
            'admin' => false,
            'maintain' => false,
            'push' => true,
            'triage' => true,
            'pull' => true,
        ],
    ];

    $collaborator = Collaborator::fromArray($data);
    $array = $collaborator->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe(3);
    expect($array['login'])->toBe('bob-wilson');
    expect($array['avatar_url'])->toBe('https://github.com/avatars/bob.jpg');
    expect($array['html_url'])->toBe('https://github.com/bob-wilson');
    expect($array['permissions'])->toBeArray();
    expect($array['permissions']['push'])->toBeTrue();
    expect($array['permissions']['admin'])->toBeFalse();
});

it('can check if user has admin permission', function () {
    $data = [
        'id' => 1,
        'login' => 'admin-user',
        'avatar_url' => 'https://github.com/avatars/admin.jpg',
        'html_url' => 'https://github.com/admin-user',
        'permissions' => [
            'admin' => true,
            'maintain' => true,
            'push' => true,
            'triage' => true,
            'pull' => true,
        ],
    ];

    $collaborator = Collaborator::fromArray($data);

    expect($collaborator->isAdmin())->toBeTrue();
    expect($collaborator->canWrite())->toBeTrue();
    expect($collaborator->canRead())->toBeTrue();
});

it('can check if user has write permission', function () {
    $data = [
        'id' => 2,
        'login' => 'contributor',
        'avatar_url' => 'https://github.com/avatars/contributor.jpg',
        'html_url' => 'https://github.com/contributor',
        'permissions' => [
            'admin' => false,
            'maintain' => false,
            'push' => true,
            'triage' => false,
            'pull' => true,
        ],
    ];

    $collaborator = Collaborator::fromArray($data);

    expect($collaborator->isAdmin())->toBeFalse();
    expect($collaborator->canWrite())->toBeTrue();
    expect($collaborator->canRead())->toBeTrue();
});

it('can check if user has read-only permission', function () {
    $data = [
        'id' => 3,
        'login' => 'reader',
        'avatar_url' => 'https://github.com/avatars/reader.jpg',
        'html_url' => 'https://github.com/reader',
        'permissions' => [
            'admin' => false,
            'maintain' => false,
            'push' => false,
            'triage' => false,
            'pull' => true,
        ],
    ];

    $collaborator = Collaborator::fromArray($data);

    expect($collaborator->isAdmin())->toBeFalse();
    expect($collaborator->canWrite())->toBeFalse();
    expect($collaborator->canRead())->toBeTrue();
});

it('can check if user has specific permission level', function () {
    $data = [
        'id' => 4,
        'login' => 'maintainer',
        'avatar_url' => 'https://github.com/avatars/maintainer.jpg',
        'html_url' => 'https://github.com/maintainer',
        'permissions' => [
            'admin' => false,
            'maintain' => true,
            'push' => true,
            'triage' => true,
            'pull' => true,
        ],
    ];

    $collaborator = Collaborator::fromArray($data);

    expect($collaborator->hasPermission('admin'))->toBeFalse();
    expect($collaborator->hasPermission('maintain'))->toBeTrue();
    expect($collaborator->hasPermission('push'))->toBeTrue();
    expect($collaborator->hasPermission('triage'))->toBeTrue();
    expect($collaborator->hasPermission('pull'))->toBeTrue();
});

it('returns highest permission level', function () {
    $adminData = [
        'id' => 1,
        'login' => 'admin',
        'avatar_url' => 'https://github.com/avatars/admin.jpg',
        'html_url' => 'https://github.com/admin',
        'permissions' => [
            'admin' => true,
            'maintain' => true,
            'push' => true,
            'triage' => true,
            'pull' => true,
        ],
    ];

    $maintainerData = [
        'id' => 2,
        'login' => 'maintainer',
        'avatar_url' => 'https://github.com/avatars/maintainer.jpg',
        'html_url' => 'https://github.com/maintainer',
        'permissions' => [
            'admin' => false,
            'maintain' => true,
            'push' => true,
            'triage' => true,
            'pull' => true,
        ],
    ];

    $pushData = [
        'id' => 3,
        'login' => 'pusher',
        'avatar_url' => 'https://github.com/avatars/pusher.jpg',
        'html_url' => 'https://github.com/pusher',
        'permissions' => [
            'admin' => false,
            'maintain' => false,
            'push' => true,
            'triage' => false,
            'pull' => true,
        ],
    ];

    $triageData = [
        'id' => 4,
        'login' => 'triager',
        'avatar_url' => 'https://github.com/avatars/triager.jpg',
        'html_url' => 'https://github.com/triager',
        'permissions' => [
            'admin' => false,
            'maintain' => false,
            'push' => false,
            'triage' => true,
            'pull' => true,
        ],
    ];

    $pullData = [
        'id' => 5,
        'login' => 'reader',
        'avatar_url' => 'https://github.com/avatars/reader.jpg',
        'html_url' => 'https://github.com/reader',
        'permissions' => [
            'admin' => false,
            'maintain' => false,
            'push' => false,
            'triage' => false,
            'pull' => true,
        ],
    ];

    $admin = Collaborator::fromArray($adminData);
    $maintainer = Collaborator::fromArray($maintainerData);
    $pusher = Collaborator::fromArray($pushData);
    $triager = Collaborator::fromArray($triageData);
    $reader = Collaborator::fromArray($pullData);

    expect($admin->getPermissionLevel())->toBe('admin');
    expect($maintainer->getPermissionLevel())->toBe('maintain');
    expect($pusher->getPermissionLevel())->toBe('push');
    expect($triager->getPermissionLevel())->toBe('triage');
    expect($reader->getPermissionLevel())->toBe('pull');
});
