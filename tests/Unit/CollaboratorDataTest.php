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
