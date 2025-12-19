<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\FileContent;
use ConduitUI\Repos\Services\ContentQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new ContentQuery($this->connector, 'owner/repo');
});

afterEach(function () {
    m::close();
});

it('can set file path', function () {
    $result = $this->query->path('README.md');

    expect($result)->toBeInstanceOf(ContentQuery::class);
});

it('can set ref (branch/tag/commit)', function () {
    $result = $this->query->ref('develop');

    expect($result)->toBeInstanceOf(ContentQuery::class);
});

it('can get file content', function () {
    $responseData = [
        'name' => 'README.md',
        'path' => 'README.md',
        'sha' => 'abc123',
        'size' => 100,
        'type' => 'file',
        'content' => base64_encode('# Test README'),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/main/README.md',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/README.md',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/README.md', [])
        ->andReturn($response);

    $file = $this->query->path('README.md')->get();

    expect($file)->toBeInstanceOf(FileContent::class);
    expect($file->name)->toBe('README.md');
    expect($file->path)->toBe('README.md');
    expect($file->sha)->toBe('abc123');
});

it('can get file content with ref', function () {
    $responseData = [
        'name' => 'config.json',
        'path' => 'config.json',
        'sha' => 'def456',
        'size' => 50,
        'type' => 'file',
        'content' => base64_encode('{}'),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/develop/config.json',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/develop/config.json',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/config.json', ['ref' => 'develop'])
        ->andReturn($response);

    $file = $this->query->path('config.json')->ref('develop')->get();

    expect($file)->toBeInstanceOf(FileContent::class);
    expect($file->name)->toBe('config.json');
});

it('can get decoded content directly', function () {
    $content = '# Test README';
    $responseData = [
        'name' => 'README.md',
        'path' => 'README.md',
        'sha' => 'abc123',
        'size' => strlen($content),
        'type' => 'file',
        'content' => base64_encode($content),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/main/README.md',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/README.md',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/README.md', [])
        ->andReturn($response);

    $decoded = $this->query->path('README.md')->content();

    expect($decoded)->toBe($content);
});

it('can get raw content using raw method', function () {
    $content = '# Test README';
    $responseData = [
        'name' => 'README.md',
        'path' => 'README.md',
        'sha' => 'abc123',
        'size' => strlen($content),
        'type' => 'file',
        'content' => base64_encode($content),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/main/README.md',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/README.md',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/README.md', [])
        ->andReturn($response);

    $raw = $this->query->path('README.md')->raw();

    expect($raw)->toBe($content);
});

it('can get json content as array', function () {
    $jsonData = ['key' => 'value', 'number' => 42];
    $content = json_encode($jsonData);
    $responseData = [
        'name' => 'config.json',
        'path' => 'config.json',
        'sha' => 'abc123',
        'size' => strlen($content),
        'type' => 'file',
        'content' => base64_encode($content),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/main/config.json',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/config.json',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/config.json', [])
        ->andReturn($response);

    $parsed = $this->query->path('config.json')->json();

    expect($parsed)->toBe($jsonData);
});

it('can get directory contents', function () {
    $responseData = [
        [
            'name' => 'file1.txt',
            'path' => 'dir/file1.txt',
            'sha' => 'sha1',
            'size' => 100,
            'type' => 'file',
            'html_url' => 'https://github.com/owner/repo/blob/main/dir/file1.txt',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/dir/file1.txt',
        ],
        [
            'name' => 'file2.txt',
            'path' => 'dir/file2.txt',
            'sha' => 'sha2',
            'size' => 200,
            'type' => 'file',
            'html_url' => 'https://github.com/owner/repo/blob/main/dir/file2.txt',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/dir/file2.txt',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/dir', [])
        ->andReturn($response);

    $files = $this->query->path('dir')->list();

    expect($files)->toHaveCount(2);
    expect($files->first())->toBeInstanceOf(FileContent::class);
    expect($files->first()->name)->toBe('file1.txt');
    expect($files->last()->name)->toBe('file2.txt');
});

it('can create a file', function () {
    $content = '# New README';
    $message = 'Add README';
    $responseData = [
        'content' => [
            'name' => 'README.md',
            'path' => 'README.md',
            'sha' => 'newsha123',
            'size' => strlen($content),
            'type' => 'file',
            'html_url' => 'https://github.com/owner/repo/blob/main/README.md',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/README.md',
        ],
        'commit' => [
            'sha' => 'commitsha',
            'message' => $message,
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/README.md', [
            'message' => $message,
            'content' => base64_encode($content),
        ])
        ->andReturn($response);

    $file = $this->query->path('README.md')->create($content, $message);

    expect($file)->toBeInstanceOf(FileContent::class);
    expect($file->name)->toBe('README.md');
    expect($file->sha)->toBe('newsha123');
});

it('can create a file with branch', function () {
    $content = 'test content';
    $message = 'Add file';
    $branch = 'develop';
    $responseData = [
        'content' => [
            'name' => 'test.txt',
            'path' => 'test.txt',
            'sha' => 'sha123',
            'size' => strlen($content),
            'type' => 'file',
            'html_url' => 'https://github.com/owner/repo/blob/develop/test.txt',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/develop/test.txt',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/test.txt', [
            'message' => $message,
            'content' => base64_encode($content),
            'branch' => $branch,
        ])
        ->andReturn($response);

    $file = $this->query->path('test.txt')->create($content, $message, $branch);

    expect($file)->toBeInstanceOf(FileContent::class);
});

it('can update a file', function () {
    $content = 'Updated content';
    $message = 'Update file';
    $sha = 'oldsha123';
    $responseData = [
        'content' => [
            'name' => 'file.txt',
            'path' => 'file.txt',
            'sha' => 'newsha456',
            'size' => strlen($content),
            'type' => 'file',
            'html_url' => 'https://github.com/owner/repo/blob/main/file.txt',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/file.txt',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/file.txt', [
            'message' => $message,
            'content' => base64_encode($content),
            'sha' => $sha,
        ])
        ->andReturn($response);

    $file = $this->query->path('file.txt')->update($content, $sha, $message);

    expect($file)->toBeInstanceOf(FileContent::class);
    expect($file->sha)->toBe('newsha456');
});

it('can update a file with branch', function () {
    $content = 'Updated content';
    $message = 'Update file';
    $sha = 'oldsha123';
    $branch = 'develop';
    $responseData = [
        'content' => [
            'name' => 'file.txt',
            'path' => 'file.txt',
            'sha' => 'newsha456',
            'size' => strlen($content),
            'type' => 'file',
            'html_url' => 'https://github.com/owner/repo/blob/develop/file.txt',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/develop/file.txt',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/file.txt', [
            'message' => $message,
            'content' => base64_encode($content),
            'sha' => $sha,
            'branch' => $branch,
        ])
        ->andReturn($response);

    $file = $this->query->path('file.txt')->update($content, $sha, $message, $branch);

    expect($file)->toBeInstanceOf(FileContent::class);
});

it('can delete a file', function () {
    $message = 'Delete file';
    $sha = 'sha123';
    $responseData = [
        'commit' => [
            'sha' => 'commitsha',
            'message' => $message,
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/contents/file.txt', [
            'message' => $message,
            'sha' => $sha,
        ])
        ->andReturn($response);

    $result = $this->query->path('file.txt')->delete($sha, $message);

    expect($result)->toBeTrue();
});

it('can delete a file with branch', function () {
    $message = 'Delete file';
    $sha = 'sha123';
    $branch = 'develop';
    $responseData = [
        'commit' => [
            'sha' => 'commitsha',
            'message' => $message,
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/contents/file.txt', [
            'message' => $message,
            'sha' => $sha,
            'branch' => $branch,
        ])
        ->andReturn($response);

    $result = $this->query->path('file.txt')->delete($sha, $message, $branch);

    expect($result)->toBeTrue();
});

it('can download file content', function () {
    $content = 'File content to download';
    $responseData = [
        'name' => 'download.txt',
        'path' => 'download.txt',
        'sha' => 'abc123',
        'size' => strlen($content),
        'type' => 'file',
        'content' => base64_encode($content),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/main/download.txt',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/download.txt',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/download.txt', [])
        ->andReturn($response);

    $downloaded = $this->query->path('download.txt')->download();

    expect($downloaded)->toBe($content);
});

it('can chain multiple fluent methods', function () {
    $content = 'Test content';
    $responseData = [
        'name' => 'test.txt',
        'path' => 'src/test.txt',
        'sha' => 'abc123',
        'size' => strlen($content),
        'type' => 'file',
        'content' => base64_encode($content),
        'encoding' => 'base64',
        'html_url' => 'https://github.com/owner/repo/blob/develop/src/test.txt',
        'download_url' => 'https://raw.githubusercontent.com/owner/repo/develop/src/test.txt',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src/test.txt', ['ref' => 'develop'])
        ->andReturn($response);

    $file = $this->query
        ->path('src/test.txt')
        ->ref('develop')
        ->get();

    expect($file)->toBeInstanceOf(FileContent::class);
    expect($file->path)->toBe('src/test.txt');
});
