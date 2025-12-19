<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\BranchQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new BranchQuery($this->connector, 'owner', 'repo');
});

afterEach(function () {
    m::close();
});

it('can get all branches', function () {
    $responseData = [
        [
            'name' => 'main',
            'protected' => true,
            'commit' => [
                'sha' => 'abc123',
                'url' => 'https://api.github.com/repos/owner/repo/commits/abc123',
            ],
        ],
        [
            'name' => 'develop',
            'protected' => false,
            'commit' => [
                'sha' => 'def456',
                'url' => 'https://api.github.com/repos/owner/repo/commits/def456',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/branches', [])
        ->andReturn($response);

    $branches = $this->query->get();

    expect($branches)->toHaveCount(2);
    expect($branches->first()->name)->toBe('main');
    expect($branches->last()->name)->toBe('develop');
});

it('can filter protected branches', function () {
    $responseData = [
        [
            'name' => 'main',
            'protected' => true,
            'commit' => [
                'sha' => 'abc123',
                'url' => 'https://api.github.com/repos/owner/repo/commits/abc123',
            ],
        ],
        [
            'name' => 'develop',
            'protected' => false,
            'commit' => [
                'sha' => 'def456',
                'url' => 'https://api.github.com/repos/owner/repo/commits/def456',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/branches', [])
        ->andReturn($response);

    $branches = $this->query->whereProtected()->get();

    expect($branches)->toHaveCount(1);
    expect($branches->first()->name)->toBe('main');
    expect($branches->first()->protected)->toBeTrue();
});

it('can filter non-protected branches', function () {
    $responseData = [
        [
            'name' => 'main',
            'protected' => true,
            'commit' => [
                'sha' => 'abc123',
                'url' => 'https://api.github.com/repos/owner/repo/commits/abc123',
            ],
        ],
        [
            'name' => 'develop',
            'protected' => false,
            'commit' => [
                'sha' => 'def456',
                'url' => 'https://api.github.com/repos/owner/repo/commits/def456',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/branches', [])
        ->andReturn($response);

    $branches = $this->query->whereNotProtected()->get();

    expect($branches)->toHaveCount(1);
    expect($branches->first()->name)->toBe('develop');
    expect($branches->first()->protected)->toBeFalse();
});

it('can filter by branch name', function () {
    $responseData = [
        [
            'name' => 'main',
            'protected' => true,
            'commit' => [
                'sha' => 'abc123',
                'url' => 'https://api.github.com/repos/owner/repo/commits/abc123',
            ],
        ],
        [
            'name' => 'develop',
            'protected' => false,
            'commit' => [
                'sha' => 'def456',
                'url' => 'https://api.github.com/repos/owner/repo/commits/def456',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/branches', [])
        ->andReturn($response);

    $branches = $this->query->whereName('main')->get();

    expect($branches)->toHaveCount(1);
    expect($branches->first()->name)->toBe('main');
});

it('can chain multiple filters', function () {
    $responseData = [
        [
            'name' => 'main',
            'protected' => true,
            'commit' => [
                'sha' => 'abc123',
                'url' => 'https://api.github.com/repos/owner/repo/commits/abc123',
            ],
        ],
        [
            'name' => 'staging',
            'protected' => true,
            'commit' => [
                'sha' => 'ghi789',
                'url' => 'https://api.github.com/repos/owner/repo/commits/ghi789',
            ],
        ],
        [
            'name' => 'develop',
            'protected' => false,
            'commit' => [
                'sha' => 'def456',
                'url' => 'https://api.github.com/repos/owner/repo/commits/def456',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/branches', [])
        ->andReturn($response);

    $branches = $this->query->whereProtected()->whereName('main')->get();

    expect($branches)->toHaveCount(1);
    expect($branches->first()->name)->toBe('main');
    expect($branches->first()->protected)->toBeTrue();
});

it('can get a single branch by name', function () {
    $responseData = [
        'name' => 'main',
        'protected' => true,
        'commit' => [
            'sha' => 'abc123',
            'url' => 'https://api.github.com/repos/owner/repo/commits/abc123',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/branches/main')
        ->andReturn($response);

    $branch = $this->query->find('main');

    expect($branch->name)->toBe('main');
    expect($branch->protected)->toBeTrue();
});

it('returns empty collection when no branches match filters', function () {
    $responseData = [
        [
            'name' => 'main',
            'protected' => true,
            'commit' => [
                'sha' => 'abc123',
                'url' => 'https://api.github.com/repos/owner/repo/commits/abc123',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/branches', [])
        ->andReturn($response);

    $branches = $this->query->whereName('nonexistent')->get();

    expect($branches)->toBeEmpty();
});

it('can create a new branch', function () {
    $requestData = [
        'ref' => 'refs/heads/feature-branch',
        'sha' => 'abc123',
    ];

    $responseData = [
        'ref' => 'refs/heads/feature-branch',
        'object' => [
            'sha' => 'abc123',
            'url' => 'https://api.github.com/repos/owner/repo/git/commits/abc123',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/git/refs', $requestData)
        ->andReturn($response);

    $result = $this->query->create('feature-branch', 'abc123');

    expect($result)->toBeTrue();
});

it('can delete a branch', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/git/refs/heads/feature-branch')
        ->andReturn($response);

    $result = $this->query->delete('feature-branch');

    expect($result)->toBeTrue();
});

it('returns false when delete fails', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/git/refs/heads/feature-branch')
        ->andReturn($response);

    $result = $this->query->delete('feature-branch');

    expect($result)->toBeFalse();
});
