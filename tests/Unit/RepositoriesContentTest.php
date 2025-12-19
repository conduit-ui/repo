<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\FileContent;
use ConduitUI\Repos\Services\ContentQuery;
use ConduitUI\Repos\Services\Repositories;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->service = new Repositories($this->connector);
});

afterEach(function () {
    m::close();
});

it('can create content query', function () {
    $query = $this->service->contentQuery('owner/repo');

    expect($query)->toBeInstanceOf(ContentQuery::class);
});

it('can create file query with path', function () {
    $query = $this->service->file('owner/repo', 'README.md');

    expect($query)->toBeInstanceOf(ContentQuery::class);
});

it('can get file through repositories service', function () {
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

    $file = $this->service->file('owner/repo', 'README.md')->get();

    expect($file)->toBeInstanceOf(FileContent::class);
    expect($file->name)->toBe('README.md');
    expect($file->decoded())->toBe($content);
});

it('can get file content directly through repositories service', function () {
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

    $result = $this->service->file('owner/repo', 'README.md')->content();

    expect($result)->toBe($content);
});

it('can create file through repositories service', function () {
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

    $file = $this->service->file('owner/repo', 'README.md')->create($content, $message);

    expect($file)->toBeInstanceOf(FileContent::class);
    expect($file->name)->toBe('README.md');
});

it('can update file through repositories service', function () {
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

    $file = $this->service->file('owner/repo', 'file.txt')->update($content, $sha, $message);

    expect($file)->toBeInstanceOf(FileContent::class);
    expect($file->sha)->toBe('newsha456');
});

it('can delete file through repositories service', function () {
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

    $result = $this->service->file('owner/repo', 'file.txt')->delete($sha, $message);

    expect($result)->toBeTrue();
});

it('can list directory contents through repositories service', function () {
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
        ->with('/repos/owner/repo/contents/src', [])
        ->andReturn($response);

    $files = $this->service->file('owner/repo', 'src')->list();

    expect($files)->toHaveCount(2);
    expect($files->first())->toBeInstanceOf(FileContent::class);
});
