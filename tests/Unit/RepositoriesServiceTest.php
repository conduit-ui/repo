<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\Repositories;
use ConduitUI\Repos\Services\RepositoryQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->service = new Repositories($this->connector);
});

afterEach(function () {
    m::close();
});

it('can find a repository', function () {
    $responseData = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo')
        ->andReturn($response);

    $repo = $this->service->find('owner/test-repo');

    expect($repo->name)->toBe('test-repo');
    expect($repo->fullName)->toBe('owner/test-repo');
});

it('can create query for user', function () {
    $query = $this->service->forUser('testuser');

    expect($query)->toBeInstanceOf(RepositoryQuery::class);
});

it('can create query for org', function () {
    $query = $this->service->forOrg('testorg');

    expect($query)->toBeInstanceOf(RepositoryQuery::class);
});

it('can create query for authenticated user', function () {
    $query = $this->service->forAuthenticatedUser();

    expect($query)->toBeInstanceOf(RepositoryQuery::class);
});

it('can create a repository for user', function () {
    $attributes = [
        'name' => 'new-repo',
        'description' => 'A new repository',
        'private' => false,
    ];

    $responseData = [
        'id' => 2,
        'name' => 'new-repo',
        'full_name' => 'user/new-repo',
        'html_url' => 'https://github.com/user/new-repo',
        'clone_url' => 'https://github.com/user/new-repo.git',
        'ssh_url' => 'git@github.com:user/new-repo.git',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/user/repos', $attributes)
        ->andReturn($response);

    $repo = $this->service->create($attributes);

    expect($repo->name)->toBe('new-repo');
});

it('can create a repository for org', function () {
    $attributes = [
        'org' => 'myorg',
        'name' => 'org-repo',
        'description' => 'An org repository',
    ];

    $responseData = [
        'id' => 3,
        'name' => 'org-repo',
        'full_name' => 'myorg/org-repo',
        'html_url' => 'https://github.com/myorg/org-repo',
        'clone_url' => 'https://github.com/myorg/org-repo.git',
        'ssh_url' => 'git@github.com:myorg/org-repo.git',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/orgs/myorg/repos', ['name' => 'org-repo', 'description' => 'An org repository'])
        ->andReturn($response);

    $repo = $this->service->create($attributes);

    expect($repo->name)->toBe('org-repo');
});

it('can update a repository', function () {
    $attributes = [
        'description' => 'Updated description',
        'private' => true,
    ];

    $responseData = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'description' => 'Updated description',
        'private' => true,
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('patch')
        ->with('/repos/owner/test-repo', $attributes)
        ->andReturn($response);

    $repo = $this->service->update('owner/test-repo', $attributes);

    expect($repo->description)->toBe('Updated description');
});

it('can delete a repository', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/test-repo')
        ->andReturn($response);

    $result = $this->service->delete('owner/test-repo');

    expect($result)->toBeTrue();
});

it('can get branches', function () {
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
        ->with('/repos/owner/test-repo/branches')
        ->andReturn($response);

    $branches = $this->service->branches('owner/test-repo');

    expect($branches)->toHaveCount(2);
    expect($branches->first()->name)->toBe('main');
    expect($branches->last()->name)->toBe('develop');
});

it('can get releases', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo/releases')
        ->andReturn($response);

    $releases = $this->service->releases('owner/test-repo');

    expect($releases)->toHaveCount(1);
    expect($releases->first()->tagName)->toBe('v1.0.0');
});

it('can get collaborators', function () {
    $responseData = [
        [
            'id' => 1,
            'login' => 'collaborator1',
            'avatar_url' => 'https://github.com/avatars/collaborator1.jpg',
            'html_url' => 'https://github.com/collaborator1',
            'permissions' => [
                'admin' => true,
                'push' => true,
                'pull' => true,
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo/collaborators')
        ->andReturn($response);

    $collaborators = $this->service->collaborators('owner/test-repo');

    expect($collaborators)->toHaveCount(1);
    expect($collaborators->first()->login)->toBe('collaborator1');
});

it('can get topics', function () {
    $responseData = [
        'names' => ['php', 'laravel', 'package'],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo/topics', [], ['Accept' => 'application/vnd.github.mercy-preview+json'])
        ->andReturn($response);

    $topics = $this->service->topics('owner/test-repo');

    expect($topics)->toBe(['php', 'laravel', 'package']);
});

it('can get topics when empty', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn([]);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo/topics', [], ['Accept' => 'application/vnd.github.mercy-preview+json'])
        ->andReturn($response);

    $topics = $this->service->topics('owner/test-repo');

    expect($topics)->toBe([]);
});

it('can get languages', function () {
    $responseData = [
        'PHP' => 50000,
        'JavaScript' => 25000,
        'CSS' => 10000,
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo/languages')
        ->andReturn($response);

    $languages = $this->service->languages('owner/test-repo');

    expect($languages)->toBe($responseData);
});

it('can create a query', function () {
    $query = $this->service->query();

    expect($query)->toBeInstanceOf(RepositoryQuery::class);
});

it('can create a branch query', function () {
    $query = $this->service->branchQuery('owner/test-repo');

    expect($query)->toBeInstanceOf(\ConduitUI\Repos\Services\BranchQuery::class);
});

it('can find a specific branch', function () {
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
        ->with('/repos/owner/test-repo/branches/main')
        ->andReturn($response);

    $branch = $this->service->findBranch('owner/test-repo', 'main');

    expect($branch->name)->toBe('main');
    expect($branch->protected)->toBeTrue();
});

it('can create a branch', function () {
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
        ->with('/repos/owner/test-repo/git/refs', $requestData)
        ->andReturn($response);

    $result = $this->service->createBranch('owner/test-repo', 'feature-branch', 'abc123');

    expect($result)->toBeTrue();
});

it('can delete a branch', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/test-repo/git/refs/heads/feature-branch')
        ->andReturn($response);

    $result = $this->service->deleteBranch('owner/test-repo', 'feature-branch');

    expect($result)->toBeTrue();
});

it('can create collaborator query', function () {
    $query = $this->service->collaboratorQuery('owner/test-repo');

    expect($query)->toBeInstanceOf(\ConduitUI\Repos\Services\CollaboratorQuery::class);
});

it('can add a collaborator', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/test-repo/collaborators/newuser', ['permission' => 'push'])
        ->andReturn($response);

    $result = $this->service->addCollaborator('owner/test-repo', 'newuser', ['permission' => 'push']);

    expect($result)->toBeTrue();
});

it('can add a collaborator with default permission', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/test-repo/collaborators/newuser', [])
        ->andReturn($response);

    $result = $this->service->addCollaborator('owner/test-repo', 'newuser');

    expect($result)->toBeTrue();
});

it('can remove a collaborator', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/test-repo/collaborators/olduser')
        ->andReturn($response);

    $result = $this->service->removeCollaborator('owner/test-repo', 'olduser');

    expect($result)->toBeTrue();
});

it('can check if user is a collaborator', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo/collaborators/existinguser')
        ->andReturn($response);

    $result = $this->service->checkCollaborator('owner/test-repo', 'existinguser');

    expect($result)->toBeTrue();
});

it('returns false when user is not a collaborator', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo/collaborators/nonexistentuser')
        ->andReturn($response);

    $result = $this->service->checkCollaborator('owner/test-repo', 'nonexistentuser');

    expect($result)->toBeFalse();
});

it('can get collaborator permission level', function () {
    $responseData = [
        'permission' => 'admin',
        'user' => [
            'login' => 'testuser',
            'id' => 123,
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/test-repo/collaborators/testuser/permission')
        ->andReturn($response);

    $permission = $this->service->getCollaboratorPermission('owner/test-repo', 'testuser');

    expect($permission)->toBe('admin');
});

it('can update collaborator permission', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/test-repo/collaborators/testuser', ['permission' => 'maintain'])
        ->andReturn($response);

    $result = $this->service->updateCollaboratorPermission('owner/test-repo', 'testuser', 'maintain');

    expect($result)->toBeTrue();
});
