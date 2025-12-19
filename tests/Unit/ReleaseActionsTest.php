<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Release;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);

    // Mock the service container binding
    $this->app->instance(Connector::class, $this->connector);
});

afterEach(function () {
    m::close();
});

it('can publish a draft release', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: true,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: null,
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

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
        ->shouldReceive('patch')
        ->with('/repos/*/releases/1', ['draft' => false])
        ->andReturn($response);

    $published = $release->publish();

    expect($published)->toBeInstanceOf(Release::class);
    expect($published->draft)->toBeFalse();
});

it('can mark a release as draft', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'First Release',
        'body' => 'Release notes',
        'draft' => true,
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
        ->with('/repos/*/releases/1', ['draft' => true])
        ->andReturn($response);

    $draft = $release->markAsDraft();

    expect($draft)->toBeInstanceOf(Release::class);
    expect($draft->draft)->toBeTrue();
});

it('can mark a release as prerelease', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'First Release',
        'body' => 'Release notes',
        'draft' => false,
        'prerelease' => true,
        'created_at' => '2023-01-01T00:00:00Z',
        'published_at' => '2023-01-02T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('patch')
        ->with('/repos/*/releases/1', ['prerelease' => true])
        ->andReturn($response);

    $prerelease = $release->markAsPrerelease();

    expect($prerelease)->toBeInstanceOf(Release::class);
    expect($prerelease->prerelease)->toBeTrue();
});

it('can mark a release as latest', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

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
        ->shouldReceive('patch')
        ->with('/repos/*/releases/1', ['make_latest' => 'true'])
        ->andReturn($response);

    $latest = $release->markAsLatest();

    expect($latest)->toBeInstanceOf(Release::class);
});

it('can rename a release', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'Renamed Release',
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
        ->shouldReceive('patch')
        ->with('/repos/*/releases/1', ['name' => 'Renamed Release'])
        ->andReturn($response);

    $renamed = $release->rename('Renamed Release');

    expect($renamed)->toBeInstanceOf(Release::class);
    expect($renamed->name)->toBe('Renamed Release');
});

it('can update release body', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'First Release',
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
        ->with('/repos/*/releases/1', ['body' => 'Updated release notes'])
        ->andReturn($response);

    $updated = $release->updateBody('Updated release notes');

    expect($updated)->toBeInstanceOf(Release::class);
    expect($updated->body)->toBe('Updated release notes');
});

it('can update multiple attributes at once', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'Updated Release',
        'body' => 'Updated notes',
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
        ->with('/repos/*/releases/1', [
            'name' => 'Updated Release',
            'body' => 'Updated notes',
        ])
        ->andReturn($response);

    $updated = $release->update([
        'name' => 'Updated Release',
        'body' => 'Updated notes',
    ]);

    expect($updated)->toBeInstanceOf(Release::class);
    expect($updated->name)->toBe('Updated Release');
    expect($updated->body)->toBe('Updated notes');
});

it('can delete a release', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/*/releases/1')
        ->andReturn($response);

    $result = $release->delete();

    expect($result)->toBeTrue();
});

it('can refresh a release to get latest data', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

    $responseData = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'Updated Release',
        'body' => 'Updated notes from server',
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
        ->with('/repos/*/releases/1')
        ->andReturn($response);

    $refreshed = $release->refresh();

    expect($refreshed)->toBeInstanceOf(Release::class);
    expect($refreshed->name)->toBe('Updated Release');
    expect($refreshed->body)->toBe('Updated notes from server');
});

it('can upload an asset to a release', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

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
        ->with('/repos/*/releases/1/assets', '/path/to/asset.zip', 'asset.zip', 'application/zip')
        ->andReturn($response);

    $asset = $release->uploadAsset('/path/to/asset.zip', 'asset.zip', 'application/zip');

    expect($asset->name)->toBe('asset.zip');
    expect($asset->contentType)->toBe('application/zip');
});

it('can delete an asset from a release', function () {
    $release = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'First Release',
        body: 'Release notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00Z'),
        publishedAt: new DateTimeImmutable('2023-01-02T00:00:00Z'),
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
        assets: [],
    );

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/*/releases/assets/10')
        ->andReturn($response);

    $result = $release->deleteAsset(10);

    expect($result)->toBeTrue();
});
