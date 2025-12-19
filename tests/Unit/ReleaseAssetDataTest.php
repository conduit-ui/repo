<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\ReleaseAsset;

it('can create release asset from array', function () {
    $data = [
        'id' => 123,
        'name' => 'app-v1.0.0.zip',
        'content_type' => 'application/zip',
        'size' => 1024000,
        'download_count' => 50,
        'browser_download_url' => 'https://github.com/owner/repo/releases/download/v1.0.0/app-v1.0.0.zip',
    ];

    $asset = ReleaseAsset::fromArray($data);

    expect($asset->id)->toBe(123);
    expect($asset->name)->toBe('app-v1.0.0.zip');
    expect($asset->contentType)->toBe('application/zip');
    expect($asset->size)->toBe(1024000);
    expect($asset->downloadCount)->toBe(50);
    expect($asset->browserDownloadUrl)->toBe('https://github.com/owner/repo/releases/download/v1.0.0/app-v1.0.0.zip');
});

it('can create release asset with default download count', function () {
    $data = [
        'id' => 456,
        'name' => 'source.tar.gz',
        'content_type' => 'application/gzip',
        'size' => 500000,
        'browser_download_url' => 'https://github.com/owner/repo/releases/download/v1.0.0/source.tar.gz',
    ];

    $asset = ReleaseAsset::fromArray($data);

    expect($asset->downloadCount)->toBe(0);
});

it('can convert release asset to array', function () {
    $data = [
        'id' => 789,
        'name' => 'binary',
        'content_type' => 'application/octet-stream',
        'size' => 2048,
        'download_count' => 100,
        'browser_download_url' => 'https://github.com/owner/repo/releases/download/v2.0.0/binary',
    ];

    $asset = ReleaseAsset::fromArray($data);
    $array = $asset->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe(789);
    expect($array['name'])->toBe('binary');
    expect($array['content_type'])->toBe('application/octet-stream');
    expect($array['size'])->toBe(2048);
    expect($array['download_count'])->toBe(100);
    expect($array['browser_download_url'])->toBe('https://github.com/owner/repo/releases/download/v2.0.0/binary');
});
