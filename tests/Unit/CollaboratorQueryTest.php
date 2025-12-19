<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Collaborator;
use ConduitUI\Repos\Services\CollaboratorQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new CollaboratorQuery($this->connector, 'owner/repo');
});

afterEach(function () {
    m::close();
});

it('can get all collaborators', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'user1',
            'avatar_url' => 'https://github.com/avatars/user1.jpg',
            'html_url' => 'https://github.com/user1',
            'permissions' => [
                'admin' => true,
                'maintain' => true,
                'push' => true,
                'triage' => true,
                'pull' => true,
            ],
        ],
        [
            'id' => 2,
            'login' => 'user2',
            'avatar_url' => 'https://github.com/avatars/user2.jpg',
            'html_url' => 'https://github.com/user2',
            'permissions' => [
                'admin' => false,
                'maintain' => false,
                'push' => true,
                'triage' => false,
                'pull' => true,
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->get();

    expect($result)->toHaveCount(2);
    expect($result->first())->toBeInstanceOf(Collaborator::class);
    expect($result->first()->login)->toBe('user1');
    expect($result->last()->login)->toBe('user2');
});

it('can filter collaborators by permission', function () {
    $responseData = [
        [
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
        ],
        [
            'id' => 2,
            'login' => 'push-user',
            'avatar_url' => 'https://github.com/avatars/push.jpg',
            'html_url' => 'https://github.com/push-user',
            'permissions' => [
                'admin' => false,
                'maintain' => false,
                'push' => true,
                'triage' => false,
                'pull' => true,
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->wherePermission('admin')->get();

    expect($result)->toHaveCount(1);
    expect($result->first()->login)->toBe('admin-user');
    expect($result->first()->permissions->admin)->toBeTrue();
});

it('can filter collaborators by push permission', function () {
    $responseData = [
        [
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
        ],
        [
            'id' => 2,
            'login' => 'read-only-user',
            'avatar_url' => 'https://github.com/avatars/readonly.jpg',
            'html_url' => 'https://github.com/read-only-user',
            'permissions' => [
                'admin' => false,
                'maintain' => false,
                'push' => false,
                'triage' => false,
                'pull' => true,
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->wherePermission('push')->get();

    expect($result)->toHaveCount(1);
    expect($result->first()->login)->toBe('admin-user');
});

it('can filter collaborators by maintain permission', function () {
    $responseData = [
        [
            'id' => 1,
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
        ],
        [
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
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->wherePermission('maintain')->get();

    expect($result)->toHaveCount(1);
    expect($result->first()->login)->toBe('maintainer');
});

it('can filter collaborators by triage permission', function () {
    $responseData = [
        [
            'id' => 1,
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
        ],
        [
            'id' => 2,
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
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->wherePermission('triage')->get();

    expect($result)->toHaveCount(1);
    expect($result->first()->login)->toBe('triager');
});

it('can filter collaborators by pull permission', function () {
    $responseData = [
        [
            'id' => 1,
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
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->wherePermission('pull')->get();

    expect($result)->toHaveCount(1);
    expect($result->first()->login)->toBe('reader');
    expect($result->first()->permissions->pull)->toBeTrue();
});

it('returns empty collection when no collaborators match permission filter', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'read-only',
            'avatar_url' => 'https://github.com/avatars/readonly.jpg',
            'html_url' => 'https://github.com/read-only',
            'permissions' => [
                'admin' => false,
                'maintain' => false,
                'push' => false,
                'triage' => false,
                'pull' => true,
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->wherePermission('admin')->get();

    expect($result)->toHaveCount(0);
    expect($result->isEmpty())->toBeTrue();
});

it('can apply limit to results', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'user1',
            'avatar_url' => 'https://github.com/avatars/user1.jpg',
            'html_url' => 'https://github.com/user1',
            'permissions' => ['admin' => true, 'maintain' => true, 'push' => true, 'triage' => true, 'pull' => true],
        ],
        [
            'id' => 2,
            'login' => 'user2',
            'avatar_url' => 'https://github.com/avatars/user2.jpg',
            'html_url' => 'https://github.com/user2',
            'permissions' => ['admin' => false, 'maintain' => false, 'push' => true, 'triage' => false, 'pull' => true],
        ],
        [
            'id' => 3,
            'login' => 'user3',
            'avatar_url' => 'https://github.com/avatars/user3.jpg',
            'html_url' => 'https://github.com/user3',
            'permissions' => ['admin' => false, 'maintain' => false, 'push' => false, 'triage' => false, 'pull' => true],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->limit(2)->get();

    expect($result)->toHaveCount(2);
    expect($result->first()->login)->toBe('user1');
    expect($result->last()->login)->toBe('user2');
});

it('can chain permission filter and limit', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'pusher1',
            'avatar_url' => 'https://github.com/avatars/pusher1.jpg',
            'html_url' => 'https://github.com/pusher1',
            'permissions' => ['admin' => false, 'maintain' => false, 'push' => true, 'triage' => false, 'pull' => true],
        ],
        [
            'id' => 2,
            'login' => 'pusher2',
            'avatar_url' => 'https://github.com/avatars/pusher2.jpg',
            'html_url' => 'https://github.com/pusher2',
            'permissions' => ['admin' => false, 'maintain' => false, 'push' => true, 'triage' => false, 'pull' => true],
        ],
        [
            'id' => 3,
            'login' => 'reader',
            'avatar_url' => 'https://github.com/avatars/reader.jpg',
            'html_url' => 'https://github.com/reader',
            'permissions' => ['admin' => false, 'maintain' => false, 'push' => false, 'triage' => false, 'pull' => true],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->wherePermission('push')->limit(1)->get();

    expect($result)->toHaveCount(1);
    expect($result->first()->login)->toBe('pusher1');
});

it('returns empty collection when api returns empty array', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn([]);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->get();

    expect($result)->toHaveCount(0);
    expect($result->isEmpty())->toBeTrue();
});

it('can get first collaborator', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'first-user',
            'avatar_url' => 'https://github.com/avatars/first.jpg',
            'html_url' => 'https://github.com/first-user',
            'permissions' => ['admin' => true, 'maintain' => true, 'push' => true, 'triage' => true, 'pull' => true],
        ],
        [
            'id' => 2,
            'login' => 'second-user',
            'avatar_url' => 'https://github.com/avatars/second.jpg',
            'html_url' => 'https://github.com/second-user',
            'permissions' => ['admin' => false, 'maintain' => false, 'push' => true, 'triage' => false, 'pull' => true],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->first();

    expect($result)->toBeInstanceOf(Collaborator::class);
    expect($result->login)->toBe('first-user');
});

it('returns null when no collaborators found for first', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn([]);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', [])
        ->andReturn($response);

    $result = $this->query->first();

    expect($result)->toBeNull();
});

it('can pass affiliation parameter', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'user1',
            'avatar_url' => 'https://github.com/avatars/user1.jpg',
            'html_url' => 'https://github.com/user1',
            'permissions' => ['admin' => true, 'maintain' => true, 'push' => true, 'triage' => true, 'pull' => true],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', ['affiliation' => 'direct'])
        ->andReturn($response);

    $result = $this->query->affiliation('direct')->get();

    expect($result)->toHaveCount(1);
});

it('can pass permission parameter', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'admin',
            'avatar_url' => 'https://github.com/avatars/admin.jpg',
            'html_url' => 'https://github.com/admin',
            'permissions' => ['admin' => true, 'maintain' => true, 'push' => true, 'triage' => true, 'pull' => true],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', ['permission' => 'admin'])
        ->andReturn($response);

    $result = $this->query->permission('admin')->get();

    expect($result)->toHaveCount(1);
});

it('can combine affiliation and permission parameters', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'admin',
            'avatar_url' => 'https://github.com/avatars/admin.jpg',
            'html_url' => 'https://github.com/admin',
            'permissions' => ['admin' => true, 'maintain' => true, 'push' => true, 'triage' => true, 'pull' => true],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/collaborators', ['affiliation' => 'direct', 'permission' => 'admin'])
        ->andReturn($response);

    $result = $this->query->affiliation('direct')->permission('admin')->get();

    expect($result)->toHaveCount(1);
});
