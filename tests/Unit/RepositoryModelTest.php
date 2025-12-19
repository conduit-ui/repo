<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Repository;
use ConduitUI\Repos\Services\Repositories;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->repositories = new Repositories($this->connector);

    // Bind to container for testing
    app()->instance(Repositories::class, $this->repositories);
});

afterEach(function () {
    m::close();
});

it('can archive a repository', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'archived' => false,
    ]);

    $result = $repo->archive();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can unarchive a repository', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'archived' => true,
    ]);

    $result = $repo->unarchive();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can make repository private', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'private' => false,
    ]);

    $result = $repo->makePrivate();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can make repository public', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'private' => true,
    ]);

    $result = $repo->makePublic();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can transfer repository', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->transfer('new-owner');

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can rename repository', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->rename('new-name');

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can enable wiki', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->enableWiki();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can disable wiki', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->disableWiki();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can enable issues', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->enableIssues();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can disable issues', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->disableIssues();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can enable projects', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->enableProjects();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can disable projects', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->disableProjects();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can enable discussions', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->enableDiscussions();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can disable discussions', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->disableDiscussions();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can set topics', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'topics' => [],
    ]);

    $result = $repo->setTopics(['php', 'laravel']);

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can add topic', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'topics' => ['php'],
    ]);

    $result = $repo->addTopic('laravel');

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can remove topic', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'topics' => ['php', 'laravel'],
    ]);

    $result = $repo->removeTopic('laravel');

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can set default branch', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'default_branch' => 'main',
    ]);

    $result = $repo->setDefaultBranch('develop');

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can update repository with batch attributes', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $result = $repo->update([
        'description' => 'New description',
        'homepage' => 'https://example.com',
    ]);

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result)->toBe($repo); // Chainable
});

it('can save pending changes to API', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'archived' => false,
    ]);

    $updatedData = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'archived' => true,
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($updatedData);

    $this->connector
        ->shouldReceive('patch')
        ->once()
        ->with('/repos/owner/test-repo', ['archived' => true])
        ->andReturn($response);

    $result = $repo->archive()->save();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result->archived)->toBeTrue();
});

it('does not call API when saving with no pending changes', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $this->connector
        ->shouldNotReceive('patch');

    $result = $repo->save();

    expect($result)->toBe($repo);
});

it('can save multiple chained changes', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'private' => false,
        'archived' => false,
    ]);

    $updatedData = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'private' => true,
        'archived' => true,
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($updatedData);

    $this->connector
        ->shouldReceive('patch')
        ->once()
        ->with('/repos/owner/test-repo', [
            'private' => true,
            'archived' => true,
        ])
        ->andReturn($response);

    $result = $repo->makePrivate()->archive()->save();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result->private)->toBeTrue();
    expect($result->archived)->toBeTrue();
});

it('can refresh repository from API', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'stargazers_count' => 100,
    ]);

    $updatedData = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'stargazers_count' => 200,
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($updatedData);

    $this->connector
        ->shouldReceive('get')
        ->once()
        ->with('/repos/owner/test-repo')
        ->andReturn($response);

    $result = $repo->refresh();

    expect($result)->toBeInstanceOf(Repository::class);
    expect($result->stargazersCount)->toBe(200);
});

it('can delete repository', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
    ]);

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->once()
        ->with('/repos/owner/test-repo')
        ->andReturn($response);

    $result = $repo->delete();

    expect($result)->toBeTrue();
});

it('has isPublic helper method', function () {
    $publicRepo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'private' => false,
    ]);

    $privateRepo = Repository::fromArray([
        'id' => 2,
        'name' => 'private-repo',
        'full_name' => 'owner/private-repo',
        'html_url' => 'https://github.com/owner/private-repo',
        'clone_url' => 'https://github.com/owner/private-repo.git',
        'ssh_url' => 'git@github.com:owner/private-repo.git',
        'private' => true,
    ]);

    expect($publicRepo->isPublic())->toBeTrue();
    expect($privateRepo->isPublic())->toBeFalse();
});

it('has isPrivate helper method', function () {
    $publicRepo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'private' => false,
    ]);

    $privateRepo = Repository::fromArray([
        'id' => 2,
        'name' => 'private-repo',
        'full_name' => 'owner/private-repo',
        'html_url' => 'https://github.com/owner/private-repo',
        'clone_url' => 'https://github.com/owner/private-repo.git',
        'ssh_url' => 'git@github.com:owner/private-repo.git',
        'private' => true,
    ]);

    expect($publicRepo->isPrivate())->toBeFalse();
    expect($privateRepo->isPrivate())->toBeTrue();
});

it('has isArchived helper method', function () {
    $activeRepo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'archived' => false,
    ]);

    $archivedRepo = Repository::fromArray([
        'id' => 2,
        'name' => 'archived-repo',
        'full_name' => 'owner/archived-repo',
        'html_url' => 'https://github.com/owner/archived-repo',
        'clone_url' => 'https://github.com/owner/archived-repo.git',
        'ssh_url' => 'git@github.com:owner/archived-repo.git',
        'archived' => true,
    ]);

    expect($activeRepo->isArchived())->toBeFalse();
    expect($archivedRepo->isArchived())->toBeTrue();
});

it('has isFork helper method', function () {
    $originalRepo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'fork' => false,
    ]);

    $forkedRepo = Repository::fromArray([
        'id' => 2,
        'name' => 'forked-repo',
        'full_name' => 'owner/forked-repo',
        'html_url' => 'https://github.com/owner/forked-repo',
        'clone_url' => 'https://github.com/owner/forked-repo.git',
        'ssh_url' => 'git@github.com:owner/forked-repo.git',
        'fork' => true,
    ]);

    expect($originalRepo->isFork())->toBeFalse();
    expect($forkedRepo->isFork())->toBeTrue();
});

it('has isDisabled helper method', function () {
    $enabledRepo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'disabled' => false,
    ]);

    $disabledRepo = Repository::fromArray([
        'id' => 2,
        'name' => 'disabled-repo',
        'full_name' => 'owner/disabled-repo',
        'html_url' => 'https://github.com/owner/disabled-repo',
        'clone_url' => 'https://github.com/owner/disabled-repo.git',
        'ssh_url' => 'git@github.com:owner/disabled-repo.git',
        'disabled' => true,
    ]);

    expect($enabledRepo->isDisabled())->toBeFalse();
    expect($disabledRepo->isDisabled())->toBeTrue();
});

it('clears pending changes after save', function () {
    $repo = Repository::fromArray([
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'archived' => false,
    ]);

    $updatedData = [
        'id' => 1,
        'name' => 'test-repo',
        'full_name' => 'owner/test-repo',
        'html_url' => 'https://github.com/owner/test-repo',
        'clone_url' => 'https://github.com/owner/test-repo.git',
        'ssh_url' => 'git@github.com:owner/test-repo.git',
        'archived' => true,
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($updatedData);

    $this->connector
        ->shouldReceive('patch')
        ->once()
        ->with('/repos/owner/test-repo', ['archived' => true])
        ->andReturn($response);

    $result = $repo->archive()->save();

    // Now calling save again should not make any API calls
    $this->connector
        ->shouldNotReceive('patch');

    $secondResult = $result->save();

    expect($secondResult)->toBe($result);
});
