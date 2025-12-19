<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Release;

it('can create release from array', function () {
    $data = [
        'id' => 1,
        'tag_name' => 'v1.0.0',
        'name' => 'First Release',
        'body' => 'Release notes here',
        'draft' => false,
        'prerelease' => false,
        'created_at' => '2023-01-01T00:00:00Z',
        'published_at' => '2023-01-02T00:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v1.0.0',
        'assets' => [],
    ];

    $release = Release::fromArray($data);

    expect($release->id)->toBe(1);
    expect($release->tagName)->toBe('v1.0.0');
    expect($release->name)->toBe('First Release');
    expect($release->body)->toBe('Release notes here');
    expect($release->draft)->toBeFalse();
    expect($release->prerelease)->toBeFalse();
    expect($release->createdAt)->toBeInstanceOf(DateTimeImmutable::class);
    expect($release->publishedAt)->toBeInstanceOf(DateTimeImmutable::class);
    expect($release->htmlUrl)->toBe('https://github.com/owner/repo/releases/tag/v1.0.0');
    expect($release->assets)->toBeArray();
    expect($release->assets)->toBeEmpty();
});

it('can create release with defaults', function () {
    $data = [
        'id' => 2,
        'tag_name' => 'v2.0.0',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v2.0.0',
    ];

    $release = Release::fromArray($data);

    expect($release->name)->toBe('');
    expect($release->body)->toBe('');
    expect($release->draft)->toBeFalse();
    expect($release->prerelease)->toBeFalse();
    expect($release->createdAt)->toBeNull();
    expect($release->publishedAt)->toBeNull();
    expect($release->assets)->toBeEmpty();
});

it('can create release with assets', function () {
    $data = [
        'id' => 3,
        'tag_name' => 'v3.0.0',
        'name' => 'Third Release',
        'body' => 'With assets',
        'draft' => true,
        'prerelease' => true,
        'html_url' => 'https://github.com/owner/repo/releases/tag/v3.0.0',
        'assets' => [
            [
                'id' => 1,
                'name' => 'asset.zip',
                'content_type' => 'application/zip',
                'size' => 1024,
                'download_count' => 10,
                'browser_download_url' => 'https://example.com/asset.zip',
            ],
        ],
    ];

    $release = Release::fromArray($data);

    expect($release->draft)->toBeTrue();
    expect($release->prerelease)->toBeTrue();
    expect($release->assets)->toHaveCount(1);
    expect($release->assets[0]->name)->toBe('asset.zip');
});

it('can convert release to array', function () {
    $data = [
        'id' => 4,
        'tag_name' => 'v4.0.0',
        'name' => 'Fourth Release',
        'body' => 'Test body',
        'draft' => false,
        'prerelease' => true,
        'created_at' => '2023-06-01T12:00:00Z',
        'published_at' => '2023-06-02T12:00:00Z',
        'html_url' => 'https://github.com/owner/repo/releases/tag/v4.0.0',
        'assets' => [
            [
                'id' => 2,
                'name' => 'binary',
                'content_type' => 'application/octet-stream',
                'size' => 2048,
                'download_count' => 5,
                'browser_download_url' => 'https://example.com/binary',
            ],
        ],
    ];

    $release = Release::fromArray($data);
    $array = $release->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe(4);
    expect($array['tag_name'])->toBe('v4.0.0');
    expect($array['name'])->toBe('Fourth Release');
    expect($array['body'])->toBe('Test body');
    expect($array['draft'])->toBeFalse();
    expect($array['prerelease'])->toBeTrue();
    expect($array['created_at'])->toBeString();
    expect($array['published_at'])->toBeString();
    expect($array['html_url'])->toBe('https://github.com/owner/repo/releases/tag/v4.0.0');
    expect($array['assets'])->toBeArray();
    expect($array['assets'])->toHaveCount(1);
});

it('can check if release is draft', function () {
    $draftRelease = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'Draft',
        body: 'Draft notes',
        draft: true,
        prerelease: false,
        createdAt: null,
        publishedAt: null,
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
    );

    $publishedRelease = new Release(
        id: 2,
        tagName: 'v2.0.0',
        name: 'Published',
        body: 'Published notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable,
        publishedAt: new DateTimeImmutable,
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v2.0.0',
    );

    expect($draftRelease->isDraft())->toBeTrue();
    expect($publishedRelease->isDraft())->toBeFalse();
});

it('can check if release is prerelease', function () {
    $prerelease = new Release(
        id: 1,
        tagName: 'v1.0.0-beta',
        name: 'Beta',
        body: 'Beta notes',
        draft: false,
        prerelease: true,
        createdAt: new DateTimeImmutable,
        publishedAt: new DateTimeImmutable,
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0-beta',
    );

    $stableRelease = new Release(
        id: 2,
        tagName: 'v2.0.0',
        name: 'Stable',
        body: 'Stable notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable,
        publishedAt: new DateTimeImmutable,
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v2.0.0',
    );

    expect($prerelease->isPrerelease())->toBeTrue();
    expect($stableRelease->isPrerelease())->toBeFalse();
});

it('can check if release is published', function () {
    $publishedRelease = new Release(
        id: 1,
        tagName: 'v1.0.0',
        name: 'Published',
        body: 'Published notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable,
        publishedAt: new DateTimeImmutable,
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v1.0.0',
    );

    $draftRelease = new Release(
        id: 2,
        tagName: 'v2.0.0',
        name: 'Draft',
        body: 'Draft notes',
        draft: true,
        prerelease: false,
        createdAt: new DateTimeImmutable,
        publishedAt: null,
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v2.0.0',
    );

    $notPublishedYet = new Release(
        id: 3,
        tagName: 'v3.0.0',
        name: 'Not published',
        body: 'Not published notes',
        draft: false,
        prerelease: false,
        createdAt: new DateTimeImmutable,
        publishedAt: null,
        htmlUrl: 'https://github.com/owner/repo/releases/tag/v3.0.0',
    );

    expect($publishedRelease->isPublished())->toBeTrue();
    expect($draftRelease->isPublished())->toBeFalse();
    expect($notPublishedYet->isPublished())->toBeFalse();
});
