<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\RepositoryQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new RepositoryQuery($this->connector);
});

afterEach(function () {
    m::close();
});

it('can set user', function () {
    $result = $this->query->user('testuser');

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
});

it('can set org', function () {
    $result = $this->query->org('testorg');

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
});

it('can set visibility', function () {
    $result = $this->query->visibility('public');

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
});

it('can set type', function () {
    $result = $this->query->type('owner');

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
});

it('can set sort', function () {
    $result = $this->query->sort('updated');

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
});

it('can set direction', function () {
    $result = $this->query->direction('asc');

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
});

it('can set per page', function () {
    $result = $this->query->perPage(50);

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
});

it('can set page', function () {
    $result = $this->query->page(2);

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
});

it('can get repositories for authenticated user', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'repo1',
            'full_name' => 'user/repo1',
            'html_url' => 'https://github.com/user/repo1',
            'clone_url' => 'https://github.com/user/repo1.git',
            'ssh_url' => 'git@github.com:user/repo1.git',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/user/repos', [
            'per_page' => 30,
            'page' => 1,
            'direction' => 'desc',
        ])
        ->andReturn($response);

    $repos = $this->query->get();

    expect($repos)->toHaveCount(1);
    expect($repos->first()->name)->toBe('repo1');
});

it('can get repositories for specific user', function () {
    $responseData = [
        [
            'id' => 2,
            'name' => 'repo2',
            'full_name' => 'testuser/repo2',
            'html_url' => 'https://github.com/testuser/repo2',
            'clone_url' => 'https://github.com/testuser/repo2.git',
            'ssh_url' => 'git@github.com:testuser/repo2.git',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/users/testuser/repos', [
            'per_page' => 30,
            'page' => 1,
            'direction' => 'desc',
        ])
        ->andReturn($response);

    $repos = $this->query->user('testuser')->get();

    expect($repos)->toHaveCount(1);
    expect($repos->first()->fullName)->toBe('testuser/repo2');
});

it('can get repositories for organization', function () {
    $responseData = [
        [
            'id' => 3,
            'name' => 'repo3',
            'full_name' => 'testorg/repo3',
            'html_url' => 'https://github.com/testorg/repo3',
            'clone_url' => 'https://github.com/testorg/repo3.git',
            'ssh_url' => 'git@github.com:testorg/repo3.git',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/orgs/testorg/repos', [
            'per_page' => 30,
            'page' => 1,
            'direction' => 'desc',
        ])
        ->andReturn($response);

    $repos = $this->query->org('testorg')->get();

    expect($repos)->toHaveCount(1);
    expect($repos->first()->fullName)->toBe('testorg/repo3');
});

it('can apply all query parameters', function () {
    $responseData = [
        [
            'id' => 4,
            'name' => 'repo4',
            'full_name' => 'user/repo4',
            'html_url' => 'https://github.com/user/repo4',
            'clone_url' => 'https://github.com/user/repo4.git',
            'ssh_url' => 'git@github.com:user/repo4.git',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/user/repos', [
            'per_page' => 50,
            'page' => 2,
            'visibility' => 'public',
            'type' => 'owner',
            'sort' => 'updated',
            'direction' => 'asc',
        ])
        ->andReturn($response);

    $repos = $this->query
        ->visibility('public')
        ->type('owner')
        ->sort('updated')
        ->direction('asc')
        ->perPage(50)
        ->page(2)
        ->get();

    expect($repos)->toHaveCount(1);
});

it('clears org when user is set', function () {
    $responseData = [];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/users/newuser/repos', m::any())
        ->andReturn($response);

    $this->query->org('testorg')->user('newuser')->get();

    expect(true)->toBeTrue();
});

it('clears user when org is set', function () {
    $responseData = [];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/orgs/neworg/repos', m::any())
        ->andReturn($response);

    $this->query->user('testuser')->org('neworg')->get();

    expect(true)->toBeTrue();
});

// Fluent API tests
it('can use whereOwner as alias for user', function () {
    $responseData = [];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/users/testuser/repos', m::any())
        ->andReturn($response);

    $result = $this->query->whereOwner('testuser');

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
    $result->get();
});

it('can use whereType to filter repositories', function () {
    $responseData = [];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/user/repos', m::type('array'))
        ->andReturn($response);

    $result = $this->query->whereType('public');

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
    $result->get();
});

it('filters by language client-side', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'php-repo',
            'full_name' => 'user/php-repo',
            'html_url' => 'https://github.com/user/php-repo',
            'clone_url' => 'https://github.com/user/php-repo.git',
            'ssh_url' => 'git@github.com:user/php-repo.git',
            'language' => 'PHP',
            'stargazers_count' => 50,
            'forks_count' => 10,
        ],
        [
            'id' => 2,
            'name' => 'js-repo',
            'full_name' => 'user/js-repo',
            'html_url' => 'https://github.com/user/js-repo',
            'clone_url' => 'https://github.com/user/js-repo.git',
            'ssh_url' => 'git@github.com:user/js-repo.git',
            'language' => 'JavaScript',
            'stargazers_count' => 100,
            'forks_count' => 20,
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->andReturn($response);

    $repos = $this->query->whereLanguage('PHP')->get();

    expect($repos)->toHaveCount(1);
    expect($repos->first()->language)->toBe('PHP');
});

it('filters by stars greater than client-side', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'popular-repo',
            'full_name' => 'user/popular-repo',
            'html_url' => 'https://github.com/user/popular-repo',
            'clone_url' => 'https://github.com/user/popular-repo.git',
            'ssh_url' => 'git@github.com:user/popular-repo.git',
            'stargazers_count' => 150,
            'forks_count' => 30,
        ],
        [
            'id' => 2,
            'name' => 'unpopular-repo',
            'full_name' => 'user/unpopular-repo',
            'html_url' => 'https://github.com/user/unpopular-repo',
            'clone_url' => 'https://github.com/user/unpopular-repo.git',
            'ssh_url' => 'git@github.com:user/unpopular-repo.git',
            'stargazers_count' => 50,
            'forks_count' => 5,
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->andReturn($response);

    $repos = $this->query->whereStarsGreaterThan(100)->get();

    expect($repos)->toHaveCount(1);
    expect($repos->first()->stargazersCount)->toBe(150);
});

it('filters by forks greater than client-side', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'forked-repo',
            'full_name' => 'user/forked-repo',
            'html_url' => 'https://github.com/user/forked-repo',
            'clone_url' => 'https://github.com/user/forked-repo.git',
            'ssh_url' => 'git@github.com:user/forked-repo.git',
            'stargazers_count' => 100,
            'forks_count' => 50,
        ],
        [
            'id' => 2,
            'name' => 'not-forked-repo',
            'full_name' => 'user/not-forked-repo',
            'html_url' => 'https://github.com/user/not-forked-repo',
            'clone_url' => 'https://github.com/user/not-forked-repo.git',
            'ssh_url' => 'git@github.com:user/not-forked-repo.git',
            'stargazers_count' => 100,
            'forks_count' => 5,
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->andReturn($response);

    $repos = $this->query->whereForksGreaterThan(10)->get();

    expect($repos)->toHaveCount(1);
    expect($repos->first()->forksCount)->toBe(50);
});

it('can sort by latest using sort helper', function () {
    $responseData = [];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/user/repos', m::subset([
            'sort' => 'updated',
            'direction' => 'desc',
        ]))
        ->andReturn($response);

    $result = $this->query->latest();

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
    $result->get();
});

it('can sort by oldest using sort helper', function () {
    $responseData = [];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/user/repos', m::subset([
            'sort' => 'updated',
            'direction' => 'asc',
        ]))
        ->andReturn($response);

    $result = $this->query->oldest();

    expect($result)->toBeInstanceOf(RepositoryQuery::class);
    $result->get();
});

it('can limit results client-side', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'repo1',
            'full_name' => 'user/repo1',
            'html_url' => 'https://github.com/user/repo1',
            'clone_url' => 'https://github.com/user/repo1.git',
            'ssh_url' => 'git@github.com:user/repo1.git',
        ],
        [
            'id' => 2,
            'name' => 'repo2',
            'full_name' => 'user/repo2',
            'html_url' => 'https://github.com/user/repo2',
            'clone_url' => 'https://github.com/user/repo2.git',
            'ssh_url' => 'git@github.com:user/repo2.git',
        ],
        [
            'id' => 3,
            'name' => 'repo3',
            'full_name' => 'user/repo3',
            'html_url' => 'https://github.com/user/repo3',
            'clone_url' => 'https://github.com/user/repo3.git',
            'ssh_url' => 'git@github.com:user/repo3.git',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->andReturn($response);

    $repos = $this->query->limit(2)->get();

    expect($repos)->toHaveCount(2);
});

it('can chain multiple fluent methods', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'php-repo',
            'full_name' => 'conduit-ui/php-repo',
            'html_url' => 'https://github.com/conduit-ui/php-repo',
            'clone_url' => 'https://github.com/conduit-ui/php-repo.git',
            'ssh_url' => 'git@github.com:conduit-ui/php-repo.git',
            'language' => 'PHP',
            'stargazers_count' => 150,
            'forks_count' => 30,
        ],
        [
            'id' => 2,
            'name' => 'js-repo',
            'full_name' => 'conduit-ui/js-repo',
            'html_url' => 'https://github.com/conduit-ui/js-repo',
            'clone_url' => 'https://github.com/conduit-ui/js-repo.git',
            'ssh_url' => 'git@github.com:conduit-ui/js-repo.git',
            'language' => 'JavaScript',
            'stargazers_count' => 50,
            'forks_count' => 10,
        ],
        [
            'id' => 3,
            'name' => 'another-php-repo',
            'full_name' => 'conduit-ui/another-php-repo',
            'html_url' => 'https://github.com/conduit-ui/another-php-repo',
            'clone_url' => 'https://github.com/conduit-ui/another-php-repo.git',
            'ssh_url' => 'git@github.com:conduit-ui/another-php-repo.git',
            'language' => 'PHP',
            'stargazers_count' => 200,
            'forks_count' => 40,
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/users/conduit-ui/repos', m::subset([
            'type' => 'public',
            'sort' => 'updated',
            'direction' => 'desc',
        ]))
        ->andReturn($response);

    $repos = $this->query
        ->whereOwner('conduit-ui')
        ->whereType('public')
        ->whereLanguage('PHP')
        ->whereStarsGreaterThan(100)
        ->latest()
        ->limit(10)
        ->get();

    expect($repos)->toHaveCount(2);
    expect($repos->first()->language)->toBe('PHP');
    expect($repos->first()->stargazersCount)->toBeGreaterThan(100);
});
