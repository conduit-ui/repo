<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Content;

it('can create content from array', function () {
    $data = [
        'name' => 'README.md',
        'path' => 'README.md',
        'sha' => 'abc123',
        'size' => 1024,
        'type' => 'file',
        'url' => 'https://api.github.com/repos/owner/repo/contents/README.md',
        'content' => base64_encode('# README'),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/main/README.md',
        'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/abc123',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/README.md',
    ];

    $content = Content::fromArray($data);

    expect($content->name)->toBe('README.md');
    expect($content->path)->toBe('README.md');
    expect($content->sha)->toBe('abc123');
    expect($content->size)->toBe(1024);
    expect($content->type)->toBe('file');
    expect($content->url)->toBe('https://api.github.com/repos/owner/repo/contents/README.md');
    expect($content->content)->toBe(base64_encode('# README'));
    expect($content->encoding)->toBe('base64');
    expect($content->htmlUrl)->toBe('https://github.com/owner/repo/blob/main/README.md');
    expect($content->gitUrl)->toBe('https://api.github.com/repos/owner/repo/git/blobs/abc123');
    expect($content->downloadUrl)->toBe('https://raw.githubusercontent.com/owner/repo/main/README.md');
});

it('can create content with nullable fields', function () {
    $data = [
        'name' => 'folder',
        'path' => 'src/folder',
        'sha' => 'def456',
        'size' => 0,
        'type' => 'dir',
        'url' => 'https://api.github.com/repos/owner/repo/contents/src/folder',
    ];

    $content = Content::fromArray($data);

    expect($content->name)->toBe('folder');
    expect($content->content)->toBeNull();
    expect($content->encoding)->toBeNull();
    expect($content->htmlUrl)->toBeNull();
    expect($content->gitUrl)->toBeNull();
    expect($content->downloadUrl)->toBeNull();
});

it('can convert content to array', function () {
    $content = new Content(
        name: 'file.txt',
        path: 'path/file.txt',
        sha: 'abc123',
        size: 100,
        type: 'file',
        url: 'https://api.github.com/repos/owner/repo/contents/path/file.txt',
        content: base64_encode('content'),
        encoding: 'base64',
        htmlUrl: 'https://github.com/owner/repo/blob/main/path/file.txt',
        gitUrl: 'https://api.github.com/repos/owner/repo/git/blobs/abc123',
        downloadUrl: 'https://raw.githubusercontent.com/owner/repo/main/path/file.txt',
    );

    $array = $content->toArray();

    expect($array)->toBe([
        'name' => 'file.txt',
        'path' => 'path/file.txt',
        'sha' => 'abc123',
        'size' => 100,
        'type' => 'file',
        'url' => 'https://api.github.com/repos/owner/repo/contents/path/file.txt',
        'content' => base64_encode('content'),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/main/path/file.txt',
        'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/abc123',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/path/file.txt',
    ]);
});

it('can decode base64 content', function () {
    $content = new Content(
        name: 'file.txt',
        path: 'file.txt',
        sha: 'abc123',
        size: 100,
        type: 'file',
        url: 'https://api.github.com/repos/owner/repo/contents/file.txt',
        content: base64_encode('Hello, World!'),
        encoding: 'base64',
    );

    expect($content->decoded())->toBe('Hello, World!');
});

it('can decode base64 content with newlines', function () {
    $encodedContent = base64_encode('Line 1');
    $encodedWithNewlines = substr($encodedContent, 0, 4)."\n".substr($encodedContent, 4);

    $content = new Content(
        name: 'file.txt',
        path: 'file.txt',
        sha: 'abc123',
        size: 100,
        type: 'file',
        url: 'https://api.github.com/repos/owner/repo/contents/file.txt',
        content: $encodedWithNewlines,
        encoding: 'base64',
    );

    expect($content->decoded())->toBe('Line 1');
});

it('returns null when decoding content that is null', function () {
    $content = new Content(
        name: 'folder',
        path: 'src/folder',
        sha: 'abc123',
        size: 0,
        type: 'dir',
        url: 'https://api.github.com/repos/owner/repo/contents/src/folder',
        content: null,
        encoding: null,
    );

    expect($content->decoded())->toBeNull();
});

it('returns content as-is when encoding is not base64', function () {
    $content = new Content(
        name: 'file.txt',
        path: 'file.txt',
        sha: 'abc123',
        size: 100,
        type: 'file',
        url: 'https://api.github.com/repos/owner/repo/contents/file.txt',
        content: 'Plain text',
        encoding: 'utf-8',
    );

    expect($content->decoded())->toBe('Plain text');
});

it('can parse json content', function () {
    $jsonData = ['key' => 'value', 'number' => 42];
    $content = new Content(
        name: 'config.json',
        path: 'config.json',
        sha: 'abc123',
        size: 100,
        type: 'file',
        url: 'https://api.github.com/repos/owner/repo/contents/config.json',
        content: base64_encode(json_encode($jsonData)),
        encoding: 'base64',
    );

    expect($content->json())->toBe($jsonData);
});

it('returns null when parsing invalid json', function () {
    $content = new Content(
        name: 'invalid.json',
        path: 'invalid.json',
        sha: 'abc123',
        size: 100,
        type: 'file',
        url: 'https://api.github.com/repos/owner/repo/contents/invalid.json',
        content: base64_encode('not valid json'),
        encoding: 'base64',
    );

    expect($content->json())->toBeNull();
});

it('returns null when parsing json from null content', function () {
    $content = new Content(
        name: 'folder',
        path: 'src/folder',
        sha: 'abc123',
        size: 0,
        type: 'dir',
        url: 'https://api.github.com/repos/owner/repo/contents/src/folder',
        content: null,
        encoding: null,
    );

    expect($content->json())->toBeNull();
});

it('can check if content is a file', function () {
    $content = new Content(
        name: 'file.txt',
        path: 'file.txt',
        sha: 'abc123',
        size: 100,
        type: 'file',
        url: 'https://api.github.com/repos/owner/repo/contents/file.txt',
    );

    expect($content->isFile())->toBeTrue();
    expect($content->isDirectory())->toBeFalse();
    expect($content->isSymlink())->toBeFalse();
});

it('can check if content is a directory', function () {
    $content = new Content(
        name: 'folder',
        path: 'src/folder',
        sha: 'abc123',
        size: 0,
        type: 'dir',
        url: 'https://api.github.com/repos/owner/repo/contents/src/folder',
    );

    expect($content->isDirectory())->toBeTrue();
    expect($content->isFile())->toBeFalse();
    expect($content->isSymlink())->toBeFalse();
});

it('can check if content is a symlink', function () {
    $content = new Content(
        name: 'link',
        path: 'link',
        sha: 'abc123',
        size: 0,
        type: 'symlink',
        url: 'https://api.github.com/repos/owner/repo/contents/link',
    );

    expect($content->isSymlink())->toBeTrue();
    expect($content->isFile())->toBeFalse();
    expect($content->isDirectory())->toBeFalse();
});

it('returns null when decoding fails', function () {
    $content = new Content(
        name: 'file.txt',
        path: 'file.txt',
        sha: 'abc123',
        size: 100,
        type: 'file',
        url: 'https://api.github.com/repos/owner/repo/contents/file.txt',
        content: 'invalid-base64!!!',
        encoding: 'base64',
    );

    expect($content->decoded())->toBeNull();
});
