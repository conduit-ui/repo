<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\FileContent;

it('can create FileContent from array', function () {
    $data = [
        'name' => 'README.md',
        'path' => 'README.md',
        'sha' => 'abc123',
        'size' => 100,
        'type' => 'file',
        'content' => base64_encode('# Test'),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/main/README.md',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/README.md',
    ];

    $file = FileContent::fromArray($data);

    expect($file->name)->toBe('README.md');
    expect($file->path)->toBe('README.md');
    expect($file->sha)->toBe('abc123');
    expect($file->size)->toBe(100);
    expect($file->type)->toBe('file');
    expect($file->encoding)->toBe('base64');
    expect($file->htmlUrl)->toBe('https://github.com/owner/repo/blob/main/README.md');
    expect($file->downloadUrl)->toBe('https://raw.githubusercontent.com/owner/repo/main/README.md');
});

it('can create FileContent from array without optional fields', function () {
    $data = [
        'name' => 'file.txt',
        'path' => 'dir/file.txt',
        'sha' => 'def456',
        'size' => 50,
        'type' => 'file',
    ];

    $file = FileContent::fromArray($data);

    expect($file->name)->toBe('file.txt');
    expect($file->content)->toBeNull();
    expect($file->encoding)->toBeNull();
    expect($file->htmlUrl)->toBeNull();
    expect($file->downloadUrl)->toBeNull();
});

it('can convert FileContent to array', function () {
    $file = new FileContent(
        name: 'test.txt',
        path: 'src/test.txt',
        sha: 'sha123',
        size: 200,
        type: 'file',
        content: base64_encode('content'),
        encoding: 'base64',
        htmlUrl: 'https://github.com/owner/repo/blob/main/src/test.txt',
        downloadUrl: 'https://raw.githubusercontent.com/owner/repo/main/src/test.txt',
    );

    $array = $file->toArray();

    expect($array['name'])->toBe('test.txt');
    expect($array['path'])->toBe('src/test.txt');
    expect($array['sha'])->toBe('sha123');
    expect($array['size'])->toBe(200);
    expect($array['type'])->toBe('file');
    expect($array['content'])->toBe(base64_encode('content'));
    expect($array['encoding'])->toBe('base64');
    expect($array['html_url'])->toBe('https://github.com/owner/repo/blob/main/src/test.txt');
    expect($array['download_url'])->toBe('https://raw.githubusercontent.com/owner/repo/main/src/test.txt');
});

it('can decode base64 content', function () {
    $content = '# Test README';
    $file = new FileContent(
        name: 'README.md',
        path: 'README.md',
        sha: 'abc123',
        size: strlen($content),
        type: 'file',
        content: base64_encode($content),
        encoding: 'base64',
    );

    expect($file->decoded())->toBe($content);
});

it('returns null when decoding without content', function () {
    $file = new FileContent(
        name: 'README.md',
        path: 'README.md',
        sha: 'abc123',
        size: 0,
        type: 'file',
    );

    expect($file->decoded())->toBeNull();
});

it('returns content as-is when encoding is not base64', function () {
    $content = 'Plain text';
    $file = new FileContent(
        name: 'file.txt',
        path: 'file.txt',
        sha: 'abc123',
        size: strlen($content),
        type: 'file',
        content: $content,
        encoding: 'utf-8',
    );

    expect($file->decoded())->toBe($content);
});

it('can parse json content', function () {
    $jsonData = ['key' => 'value', 'number' => 42];
    $content = json_encode($jsonData);
    $file = new FileContent(
        name: 'config.json',
        path: 'config.json',
        sha: 'abc123',
        size: strlen($content),
        type: 'file',
        content: base64_encode($content),
        encoding: 'base64',
    );

    expect($file->json())->toBe($jsonData);
});

it('returns null when parsing json without content', function () {
    $file = new FileContent(
        name: 'config.json',
        path: 'config.json',
        sha: 'abc123',
        size: 0,
        type: 'file',
    );

    expect($file->json())->toBeNull();
});

it('can check if file is a directory', function () {
    $file = new FileContent(
        name: 'src',
        path: 'src',
        sha: 'abc123',
        size: 0,
        type: 'dir',
    );

    expect($file->isDirectory())->toBeTrue();
    expect($file->isFile())->toBeFalse();
});

it('can check if file is a file', function () {
    $file = new FileContent(
        name: 'README.md',
        path: 'README.md',
        sha: 'abc123',
        size: 100,
        type: 'file',
    );

    expect($file->isFile())->toBeTrue();
    expect($file->isDirectory())->toBeFalse();
});

it('can check if file is a symlink', function () {
    $file = new FileContent(
        name: 'link',
        path: 'link',
        sha: 'abc123',
        size: 0,
        type: 'symlink',
    );

    expect($file->isSymlink())->toBeTrue();
    expect($file->isFile())->toBeFalse();
    expect($file->isDirectory())->toBeFalse();
});

it('can get file extension', function () {
    $file = new FileContent(
        name: 'test.php',
        path: 'src/test.php',
        sha: 'abc123',
        size: 100,
        type: 'file',
    );

    expect($file->extension())->toBe('php');
});

it('returns null extension for files without extension', function () {
    $file = new FileContent(
        name: 'README',
        path: 'README',
        sha: 'abc123',
        size: 100,
        type: 'file',
    );

    expect($file->extension())->toBeNull();
});

it('can get base name without extension', function () {
    $file = new FileContent(
        name: 'test.php',
        path: 'src/test.php',
        sha: 'abc123',
        size: 100,
        type: 'file',
    );

    expect($file->basename())->toBe('test');
});

it('can get dirname', function () {
    $file = new FileContent(
        name: 'test.php',
        path: 'src/components/test.php',
        sha: 'abc123',
        size: 100,
        type: 'file',
    );

    expect($file->dirname())->toBe('src/components');
});

it('returns dot for dirname when file is in root', function () {
    $file = new FileContent(
        name: 'README.md',
        path: 'README.md',
        sha: 'abc123',
        size: 100,
        type: 'file',
    );

    expect($file->dirname())->toBe('.');
});
