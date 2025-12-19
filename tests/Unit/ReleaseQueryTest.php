<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\ReleaseQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new ReleaseQuery($this->connector, 'owner', 'repo');
});

afterEach(function () {
    m::close();
});

it('can get all releases', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0.0',
            'body' => 'First release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2024-01-01T00:00:00Z',
            'published_at' => '2024-01-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0',
            'name' => 'Release 0.9.0',
            'body' => 'Beta release',
            'draft' => false,
            'prerelease' => true,
            'created_at' => '2023-12-01T00:00:00Z',
            'published_at' => '2023-12-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v0.9.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->get();

    expect($releases)->toHaveCount(2);
    expect($releases->first()->tagName)->toBe('v1.0.0');
    expect($releases->last()->tagName)->toBe('v0.9.0');
});

it('can filter out drafts', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0.0',
            'body' => 'First release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2024-01-01T00:00:00Z',
            'published_at' => '2024-01-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v1.1.0',
            'name' => 'Release 1.1.0',
            'body' => 'Draft release',
            'draft' => true,
            'prerelease' => false,
            'created_at' => '2024-01-15T00:00:00Z',
            'published_at' => null,
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.1.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->whereDraft(false)->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->tagName)->toBe('v1.0.0');
    expect($releases->first()->draft)->toBeFalse();
});

it('can filter out prereleases', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0.0',
            'body' => 'First release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2024-01-01T00:00:00Z',
            'published_at' => '2024-01-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0',
            'name' => 'Release 0.9.0',
            'body' => 'Beta release',
            'draft' => false,
            'prerelease' => true,
            'created_at' => '2023-12-01T00:00:00Z',
            'published_at' => '2023-12-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v0.9.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->wherePrerelease(false)->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->tagName)->toBe('v1.0.0');
    expect($releases->first()->prerelease)->toBeFalse();
});

it('can filter only drafts', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0.0',
            'body' => 'First release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2024-01-01T00:00:00Z',
            'published_at' => '2024-01-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v1.1.0',
            'name' => 'Release 1.1.0',
            'body' => 'Draft release',
            'draft' => true,
            'prerelease' => false,
            'created_at' => '2024-01-15T00:00:00Z',
            'published_at' => null,
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.1.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->whereDraft(true)->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->tagName)->toBe('v1.1.0');
    expect($releases->first()->draft)->toBeTrue();
});

it('can filter only prereleases', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0.0',
            'body' => 'First release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2024-01-01T00:00:00Z',
            'published_at' => '2024-01-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0',
            'name' => 'Release 0.9.0',
            'body' => 'Beta release',
            'draft' => false,
            'prerelease' => true,
            'created_at' => '2023-12-01T00:00:00Z',
            'published_at' => '2023-12-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v0.9.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->wherePrerelease(true)->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->tagName)->toBe('v0.9.0');
    expect($releases->first()->prerelease)->toBeTrue();
});

it('can chain multiple filters', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0.0',
            'body' => 'First release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2024-01-01T00:00:00Z',
            'published_at' => '2024-01-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0',
            'name' => 'Release 0.9.0',
            'body' => 'Beta release',
            'draft' => false,
            'prerelease' => true,
            'created_at' => '2023-12-01T00:00:00Z',
            'published_at' => '2023-12-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v0.9.0',
            'assets' => [],
        ],
        [
            'id' => 3,
            'tag_name' => 'v1.1.0',
            'name' => 'Release 1.1.0',
            'body' => 'Draft release',
            'draft' => true,
            'prerelease' => false,
            'created_at' => '2024-01-15T00:00:00Z',
            'published_at' => null,
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.1.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->whereDraft(false)->wherePrerelease(false)->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->tagName)->toBe('v1.0.0');
});

it('can get latest release', function () {
    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'Release 1.0.0',
        'body' => 'First release',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2024-01-01T00:00:00Z',
        'published_at' => '2024-01-01T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases/latest')
        ->andReturn($response);

    $release = $this->query->latest();

    expect($release->tagName)->toBe('v1.0.0');
    expect($release->draft)->toBeFalse();
});

it('can get release by tag', function () {
    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'Release 1.0.0',
        'body' => 'First release',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2024-01-01T00:00:00Z',
        'published_at' => '2024-01-01T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases/tags/v1.0.0')
        ->andReturn($response);

    $release = $this->query->findByTag('v1.0.0');

    expect($release->tagName)->toBe('v1.0.0');
});

it('can get release by id', function () {
    $responseData = [
        'id' => 123,
        'tag_name' => 'v1.0.0',
        'name' => 'Release 1.0.0',
        'body' => 'First release',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2024-01-01T00:00:00Z',
        'published_at' => '2024-01-01T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases/123')
        ->andReturn($response);

    $release = $this->query->find(123);

    expect($release->id)->toBe(123);
    expect($release->tagName)->toBe('v1.0.0');
});

it('can create a release', function () {
    $requestData = [
        'tag_name' => 'v1.0.0',
        'name' => 'Release 1.0.0',
        'body' => 'First release',
        'draft' => false,
        'prerelease' => false,
    ];

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'Release 1.0.0',
        'body' => 'First release',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2024-01-01T00:00:00Z',
        'published_at' => '2024-01-01T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/releases', $requestData)
        ->andReturn($response);

    $release = $this->query->create($requestData);

    expect($release->tagName)->toBe('v1.0.0');
    expect($release->name)->toBe('Release 1.0.0');
});

it('can update a release', function () {
    $requestData = [
        'name' => 'Updated Release 1.0.0',
        'body' => 'Updated release notes',
    ];

    $responseData = [
        'id' => 123,
        'tag_name' => 'v1.0.0',
        'name' => 'Updated Release 1.0.0',
        'body' => 'Updated release notes',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2024-01-01T00:00:00Z',
        'published_at' => '2024-01-01T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('patch')
        ->with('/repos/owner/repo/releases/123', $requestData)
        ->andReturn($response);

    $release = $this->query->update(123, $requestData);

    expect($release->name)->toBe('Updated Release 1.0.0');
    expect($release->body)->toBe('Updated release notes');
});

it('can delete a release', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/releases/123')
        ->andReturn($response);

    $result = $this->query->delete(123);

    expect($result)->toBeTrue();
});

it('returns false when delete fails', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/releases/123')
        ->andReturn($response);

    $result = $this->query->delete(123);

    expect($result)->toBeFalse();
});

it('returns empty collection when no releases match filters', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0.0',
            'body' => 'First release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2024-01-01T00:00:00Z',
            'published_at' => '2024-01-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->whereDraft(true)->get();

    expect($releases)->toBeEmpty();
});

it('can get first release from filtered results', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Release 1.0.0',
            'body' => 'First release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2024-01-01T00:00:00Z',
            'published_at' => '2024-01-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0',
            'name' => 'Release 0.9.0',
            'body' => 'Beta release',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2023-12-01T00:00:00Z',
            'published_at' => '2023-12-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v0.9.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $release = $this->query->first();

    expect($release)->not->toBeNull();
    expect($release->tagName)->toBe('v1.0.0');
});

it('returns null when first() is called on empty results', function () {
    $responseData = [];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $release = $this->query->first();

    expect($release)->toBeNull();
});
