<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\FileContent;

it('can create file content from array', function () {
    $data = [
        'name' => 'example.php',
        'path' => 'src/example.php',
        'sha' => 'abc123def456',
        'size' => 1024,
        'url' => 'https://api.github.com/repos/owner/repo/contents/src/example.php',
        'html_url' => 'https://github.com/owner/repo/blob/main/src/example.php',
        'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/abc123def456',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/example.php',
        'type' => 'file',
        'content' => base64_encode('<?php echo "Hello World";'),
        'encoding' => 'base64',
    ];

    $file = FileContent::fromArray($data);

    expect($file->name)->toBe('example.php');
    expect($file->path)->toBe('src/example.php');
    expect($file->sha)->toBe('abc123def456');
    expect($file->size)->toBe(1024);
    expect($file->url)->toBe('https://api.github.com/repos/owner/repo/contents/src/example.php');
    expect($file->htmlUrl)->toBe('https://github.com/owner/repo/blob/main/src/example.php');
    expect($file->gitUrl)->toBe('https://api.github.com/repos/owner/repo/git/blobs/abc123def456');
    expect($file->downloadUrl)->toBe('https://raw.githubusercontent.com/owner/repo/main/src/example.php');
    expect($file->type)->toBe('file');
    expect($file->content)->toBe(base64_encode('<?php echo "Hello World";'));
    expect($file->encoding)->toBe('base64');
});

it('can handle null content', function () {
    $data = [
        'name' => 'example.php',
        'path' => 'src/example.php',
        'sha' => 'abc123def456',
        'size' => 1024,
        'url' => 'https://api.github.com/repos/owner/repo/contents/src/example.php',
        'html_url' => 'https://github.com/owner/repo/blob/main/src/example.php',
        'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/abc123def456',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/example.php',
        'type' => 'file',
    ];

    $file = FileContent::fromArray($data);

    expect($file->content)->toBeNull();
    expect($file->encoding)->toBe('none');
});

it('can convert file content to array', function () {
    $data = [
        'name' => 'example.php',
        'path' => 'src/example.php',
        'sha' => 'abc123def456',
        'size' => 1024,
        'url' => 'https://api.github.com/repos/owner/repo/contents/src/example.php',
        'html_url' => 'https://github.com/owner/repo/blob/main/src/example.php',
        'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/abc123def456',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/example.php',
        'type' => 'file',
        'content' => base64_encode('<?php echo "Hello World";'),
        'encoding' => 'base64',
    ];

    $file = FileContent::fromArray($data);
    $array = $file->toArray();

    expect($array)->toBeArray();
    expect($array['name'])->toBe('example.php');
    expect($array['path'])->toBe('src/example.php');
    expect($array['sha'])->toBe('abc123def456');
    expect($array['size'])->toBe(1024);
    expect($array['type'])->toBe('file');
    expect($array['content'])->toBe(base64_encode('<?php echo "Hello World";'));
    expect($array['encoding'])->toBe('base64');
});

it('can decode base64 content', function () {
    $originalContent = '<?php echo "Hello World";';
    $data = [
        'name' => 'example.php',
        'path' => 'src/example.php',
        'sha' => 'abc123def456',
        'size' => 1024,
        'url' => 'https://api.github.com/repos/owner/repo/contents/src/example.php',
        'html_url' => 'https://github.com/owner/repo/blob/main/src/example.php',
        'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/abc123def456',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/example.php',
        'type' => 'file',
        'content' => base64_encode($originalContent),
        'encoding' => 'base64',
    ];

    $file = FileContent::fromArray($data);

    expect($file->decodedContent())->toBe($originalContent);
});

it('returns null decoded content when content is null', function () {
    $data = [
        'name' => 'example.php',
        'path' => 'src/example.php',
        'sha' => 'abc123def456',
        'size' => 1024,
        'url' => 'https://api.github.com/repos/owner/repo/contents/src/example.php',
        'html_url' => 'https://github.com/owner/repo/blob/main/src/example.php',
        'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/abc123def456',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/example.php',
        'type' => 'file',
    ];

    $file = FileContent::fromArray($data);

    expect($file->decodedContent())->toBeNull();
});

it('returns content as-is when encoding is not base64', function () {
    $data = [
        'name' => 'example.php',
        'path' => 'src/example.php',
        'sha' => 'abc123def456',
        'size' => 1024,
        'url' => 'https://api.github.com/repos/owner/repo/contents/src/example.php',
        'html_url' => 'https://github.com/owner/repo/blob/main/src/example.php',
        'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/abc123def456',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/example.php',
        'type' => 'file',
        'content' => 'plain text content',
        'encoding' => 'utf-8',
    ];

    $file = FileContent::fromArray($data);

    expect($file->decodedContent())->toBe('plain text content');
});
