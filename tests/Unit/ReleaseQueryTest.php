<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Release;
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
            'name' => 'First Release',
            'body' => 'Release notes',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2023-01-01T00:00:00Z',
            'published_at' => '2023-01-02T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0',
            'name' => 'Beta Release',
            'body' => 'Pre-release notes',
            'draft' => false,
            'prerelease' => true,
            'created_at' => '2022-12-01T00:00:00Z',
            'published_at' => '2022-12-02T00:00:00Z',
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

it('can filter to get latest release only', function () {
    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'First Release',
        'body' => 'Release notes',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2023-01-01T00:00:00Z',
        'published_at' => '2023-01-02T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases/latest', [])
        ->andReturn($response);

    $release = $this->query->latest()->first();

    expect($release)->toBeInstanceOf(Release::class);
    expect($release->tagName)->toBe('v1.0.0');
});

it('can filter to exclude drafts', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Published Release',
            'body' => 'Release notes',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2023-01-01T00:00:00Z',
            'published_at' => '2023-01-02T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v2.0.0',
            'name' => 'Draft Release',
            'body' => 'Draft notes',
            'draft' => true,
            'prerelease' => false,
            'created_at' => '2023-02-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v2.0.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->excludeDrafts()->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->draft)->toBeFalse();
    expect($releases->first()->tagName)->toBe('v1.0.0');
});

it('can filter to include only drafts', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Published Release',
            'body' => 'Release notes',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2023-01-01T00:00:00Z',
            'published_at' => '2023-01-02T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v2.0.0',
            'name' => 'Draft Release',
            'body' => 'Draft notes',
            'draft' => true,
            'prerelease' => false,
            'created_at' => '2023-02-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v2.0.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->drafts()->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->draft)->toBeTrue();
    expect($releases->first()->tagName)->toBe('v2.0.0');
});

it('can filter to exclude prereleases', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Stable Release',
            'body' => 'Release notes',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2023-01-01T00:00:00Z',
            'published_at' => '2023-01-02T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0',
            'name' => 'Beta Release',
            'body' => 'Pre-release notes',
            'draft' => false,
            'prerelease' => true,
            'created_at' => '2022-12-01T00:00:00Z',
            'published_at' => '2022-12-02T00:00:00Z',
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

    $releases = $this->query->excludePrereleases()->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->prerelease)->toBeFalse();
    expect($releases->first()->tagName)->toBe('v1.0.0');
});

it('can filter to include only prereleases', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Stable Release',
            'body' => 'Release notes',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2023-01-01T00:00:00Z',
            'published_at' => '2023-01-02T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0',
            'name' => 'Beta Release',
            'body' => 'Pre-release notes',
            'draft' => false,
            'prerelease' => true,
            'created_at' => '2022-12-01T00:00:00Z',
            'published_at' => '2022-12-02T00:00:00Z',
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

    $releases = $this->query->prereleases()->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->prerelease)->toBeTrue();
    expect($releases->first()->tagName)->toBe('v0.9.0');
});

it('can chain multiple filters', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Stable Release',
            'body' => 'Release notes',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2023-01-01T00:00:00Z',
            'published_at' => '2023-01-02T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
            'assets' => [],
        ],
        [
            'id' => 2,
            'tag_name' => 'v0.9.0-beta',
            'name' => 'Beta Release',
            'body' => 'Pre-release notes',
            'draft' => true,
            'prerelease' => true,
            'created_at' => '2022-12-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v0.9.0-beta',
            'assets' => [],
        ],
        [
            'id' => 3,
            'tag_name' => 'v2.0.0',
            'name' => 'Future Draft',
            'body' => 'Draft notes',
            'draft' => true,
            'prerelease' => false,
            'created_at' => '2023-02-01T00:00:00Z',
            'html_url' => 'https://github.com/owner/repo/releases/tag/v2.0.0',
            'assets' => [],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases', [])
        ->andReturn($response);

    $releases = $this->query->excludeDrafts()->excludePrereleases()->get();

    expect($releases)->toHaveCount(1);
    expect($releases->first()->tagName)->toBe('v1.0.0');
    expect($releases->first()->draft)->toBeFalse();
    expect($releases->first()->prerelease)->toBeFalse();
});

it('can get a single release by tag', function () {
    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'First Release',
        'body' => 'Release notes',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2023-01-01T00:00:00Z',
        'published_at' => '2023-01-02T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/releases/tags/v1.0.0')
        ->andReturn($response);

    $release = $this->query->find('v1.0.0');

    expect($release)->toBeInstanceOf(Release::class);
    expect($release->tagName)->toBe('v1.0.0');
    expect($release->name)->toBe('First Release');
});

it('can create a release', function () {
    $requestData = [
        'tag_name' => 'v1.0.0',
        'name' => 'First Release',
        'body' => 'Release notes',
        'draft' => false,
        'prerelease' => false,
    ];

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'First Release',
        'body' => 'Release notes',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2023-01-01T00:00:00Z',
        'published_at' => '2023-01-02T00:00:00Z',
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

    expect($release)->toBeInstanceOf(Release::class);
    expect($release->tagName)->toBe('v1.0.0');
    expect($release->name)->toBe('First Release');
});

it('can update a release', function () {
    $releaseId = 1;
    $updateData = [
        'name' => 'Updated Release Name',
        'body' => 'Updated release notes',
    ];

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'Updated Release Name',
        'body' => 'Updated release notes',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2023-01-01T00:00:00Z',
        'published_at' => '2023-01-02T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('patch')
        ->with('/repos/owner/repo/releases/1', $updateData)
        ->andReturn($response);

    $release = $this->query->update($releaseId, $updateData);

    expect($release)->toBeInstanceOf(Release::class);
    expect($release->name)->toBe('Updated Release Name');
    expect($release->body)->toBe('Updated release notes');
});

it('can delete a release', function () {
    $releaseId = 1;

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/releases/1')
        ->andReturn($response);

    $result = $this->query->delete($releaseId);

    expect($result)->toBeTrue();
});

it('returns false when delete fails', function () {
    $releaseId = 1;

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/releases/1')
        ->andReturn($response);

    $result = $this->query->delete($releaseId);

    expect($result)->toBeFalse();
});

it('can upload an asset to a release', function () {
    $releaseId = 1;
    $filePath = '/path/to/asset.zip';
    $fileName = 'asset.zip';
    $contentType = 'application/zip';

    $responseData = [
        'id' => 10,
        'name' => 'asset.zip',
        'content_type' => 'application/zip',
        'size' => 1024,
        'download_count' => 0,
        'browser_download_url' => 'https://example.com/asset.zip',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('upload')
        ->with('/repos/owner/repo/releases/1/assets', $filePath, $fileName, $contentType)
        ->andReturn($response);

    $asset = $this->query->uploadAsset($releaseId, $filePath, $fileName, $contentType);

    expect($asset->name)->toBe('asset.zip');
    expect($asset->contentType)->toBe('application/zip');
});

it('can delete an asset from a release', function () {
    $assetId = 10;

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/releases/assets/10')
        ->andReturn($response);

    $result = $this->query->deleteAsset($assetId);

    expect($result)->toBeTrue();
});

it('can generate release notes', function () {
    $requestData = [
        'tag_name' => 'v1.0.0',
        'previous_tag_name' => 'v0.9.0',
    ];

    $responseData = [
        'name' => 'Release v1.0.0',
        'body' => "## What's Changed\n* Feature A by @user1\n* Bug fix B by @user2",
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/releases/generate-notes', $requestData)
        ->andReturn($response);

    $notes = $this->query->generateNotes('v1.0.0', 'v0.9.0');

    expect($notes)->toBeArray();
    expect($notes['name'])->toBe('Release v1.0.0');
    expect($notes['body'])->toContain("What's Changed");
});

it('returns empty collection when no releases match filters', function () {
    $responseData = [
        [
            'id' => 1,
            'tag_name' => 'v1.0.0',
            'name' => 'Stable Release',
            'body' => 'Release notes',
            'draft' => false,
            'prerelease' => false,
            'created_at' => '2023-01-01T00:00:00Z',
            'published_at' => '2023-01-02T00:00:00Z',
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

    $releases = $this->query->prereleases()->get();

    expect($releases)->toBeEmpty();
});
