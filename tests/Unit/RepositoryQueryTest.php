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
