<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\ContentQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new ContentQuery($this->connector, 'owner', 'repo');
});

afterEach(function () {
    m::close();
});

it('can get file contents', function () {
    $responseData = [
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

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/README.md', [])
        ->andReturn($response);

    $content = $this->query->get('README.md');

    expect($content->name)->toBe('README.md');
    expect($content->type)->toBe('file');
    expect($content->decoded())->toBe('# README');
});

it('can get file contents with ref parameter', function () {
    $responseData = [
        'name' => 'README.md',
        'path' => 'README.md',
        'sha' => 'abc123',
        'size' => 1024,
        'type' => 'file',
        'url' => 'https://api.github.com/repos/owner/repo/contents/README.md',
        'content' => base64_encode('# README'),
        'encoding' => 'base64',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/README.md', ['ref' => 'develop'])
        ->andReturn($response);

    $content = $this->query->ref('develop')->get('README.md');

    expect($content->name)->toBe('README.md');
});

it('can get directory listing', function () {
    $responseData = [
        [
            'name' => 'file1.php',
            'path' => 'src/file1.php',
            'sha' => 'abc123',
            'size' => 512,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/file1.php',
        ],
        [
            'name' => 'file2.php',
            'path' => 'src/file2.php',
            'sha' => 'def456',
            'size' => 256,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/file2.php',
        ],
        [
            'name' => 'subfolder',
            'path' => 'src/subfolder',
            'sha' => 'ghi789',
            'size' => 0,
            'type' => 'dir',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/subfolder',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src', [])
        ->andReturn($response);

    $contents = $this->query->list('src');

    expect($contents)->toHaveCount(3);
    expect($contents->first()->name)->toBe('file1.php');
    expect($contents->last()->name)->toBe('subfolder');
});

it('can filter directory listing by type', function () {
    $responseData = [
        [
            'name' => 'file1.php',
            'path' => 'src/file1.php',
            'sha' => 'abc123',
            'size' => 512,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/file1.php',
        ],
        [
            'name' => 'subfolder',
            'path' => 'src/subfolder',
            'sha' => 'ghi789',
            'size' => 0,
            'type' => 'dir',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/subfolder',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src', [])
        ->andReturn($response);

    $files = $this->query->whereType('file')->list('src');

    expect($files)->toHaveCount(1);
    expect($files->first()->name)->toBe('file1.php');
    expect($files->first()->isFile())->toBeTrue();
});

it('can filter directory listing by extension', function () {
    $responseData = [
        [
            'name' => 'file1.php',
            'path' => 'src/file1.php',
            'sha' => 'abc123',
            'size' => 512,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/file1.php',
        ],
        [
            'name' => 'file2.js',
            'path' => 'src/file2.js',
            'sha' => 'def456',
            'size' => 256,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/file2.js',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src', [])
        ->andReturn($response);

    $phpFiles = $this->query->whereExtension('php')->list('src');

    expect($phpFiles)->toHaveCount(1);
    expect($phpFiles->first()->name)->toBe('file1.php');
});

it('can create a file', function () {
    $requestData = [
        'message' => 'Add new file',
        'content' => base64_encode('File content'),
        'branch' => 'main',
    ];

    $responseData = [
        'content' => [
            'name' => 'new-file.txt',
            'path' => 'new-file.txt',
            'sha' => 'abc123',
            'size' => 12,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/new-file.txt',
        ],
        'commit' => [
            'sha' => 'def456',
            'message' => 'Add new file',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/new-file.txt', $requestData)
        ->andReturn($response);

    $result = $this->query->create('new-file.txt', [
        'content' => 'File content',
        'message' => 'Add new file',
        'branch' => 'main',
    ]);

    expect($result['content']['name'])->toBe('new-file.txt');
    expect($result['commit']['message'])->toBe('Add new file');
});

it('auto-encodes content when creating a file', function () {
    $requestData = [
        'message' => 'Add file',
        'content' => base64_encode('Content'),
    ];

    $responseData = [
        'content' => [
            'name' => 'file.txt',
            'path' => 'file.txt',
            'sha' => 'abc123',
            'size' => 7,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/file.txt',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/file.txt', $requestData)
        ->andReturn($response);

    $result = $this->query->create('file.txt', [
        'content' => 'Content',
        'message' => 'Add file',
    ]);

    expect($result['content']['name'])->toBe('file.txt');
});

it('does not double-encode already base64 content', function () {
    $base64Content = base64_encode('Already encoded');

    $requestData = [
        'message' => 'Add file',
        'content' => $base64Content,
    ];

    $responseData = [
        'content' => [
            'name' => 'file.txt',
            'path' => 'file.txt',
            'sha' => 'abc123',
            'size' => 7,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/file.txt',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/file.txt', $requestData)
        ->andReturn($response);

    $result = $this->query->create('file.txt', [
        'content' => $base64Content,
        'message' => 'Add file',
        'encoded' => true,
    ]);

    expect($result['content']['name'])->toBe('file.txt');
});

it('can update a file', function () {
    $requestData = [
        'message' => 'Update file',
        'content' => base64_encode('Updated content'),
        'sha' => 'abc123',
        'branch' => 'main',
    ];

    $responseData = [
        'content' => [
            'name' => 'file.txt',
            'path' => 'file.txt',
            'sha' => 'def456',
            'size' => 15,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/file.txt',
        ],
        'commit' => [
            'sha' => 'ghi789',
            'message' => 'Update file',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/file.txt', $requestData)
        ->andReturn($response);

    $result = $this->query->update('file.txt', [
        'content' => 'Updated content',
        'sha' => 'abc123',
        'message' => 'Update file',
        'branch' => 'main',
    ]);

    expect($result['content']['sha'])->toBe('def456');
    expect($result['commit']['message'])->toBe('Update file');
});

it('can delete a file', function () {
    $requestData = [
        'message' => 'Delete file',
        'sha' => 'abc123',
        'branch' => 'main',
    ];

    $responseData = [
        'commit' => [
            'sha' => 'def456',
            'message' => 'Delete file',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/contents/file.txt', $requestData)
        ->andReturn($response);

    $result = $this->query->delete('file.txt', [
        'sha' => 'abc123',
        'message' => 'Delete file',
        'branch' => 'main',
    ]);

    expect($result)->toBeTrue();
});

it('returns false when delete fails', function () {
    $requestData = [
        'message' => 'Delete file',
        'sha' => 'abc123',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/contents/file.txt', $requestData)
        ->andReturn($response);

    $result = $this->query->delete('file.txt', [
        'sha' => 'abc123',
        'message' => 'Delete file',
    ]);

    expect($result)->toBeFalse();
});

it('can get README', function () {
    $responseData = [
        'name' => 'README.md',
        'path' => 'README.md',
        'sha' => 'abc123',
        'size' => 1024,
        'type' => 'file',
        'url' => 'https://api.github.com/repos/owner/repo/readme',
        'content' => base64_encode('# Project'),
        'encoding' => 'base64',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/readme', [])
        ->andReturn($response);

    $readme = $this->query->readme();

    expect($readme->name)->toBe('README.md');
    expect($readme->decoded())->toBe('# Project');
});

it('can get README with ref parameter', function () {
    $responseData = [
        'name' => 'README.md',
        'path' => 'README.md',
        'sha' => 'abc123',
        'size' => 1024,
        'type' => 'file',
        'url' => 'https://api.github.com/repos/owner/repo/readme',
        'content' => base64_encode('# Project'),
        'encoding' => 'base64',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/readme', ['ref' => 'develop'])
        ->andReturn($response);

    $readme = $this->query->ref('develop')->readme();

    expect($readme->name)->toBe('README.md');
});

it('can chain ref method before get', function () {
    $responseData = [
        'name' => 'config.json',
        'path' => 'config.json',
        'sha' => 'abc123',
        'size' => 100,
        'type' => 'file',
        'url' => 'https://api.github.com/repos/owner/repo/contents/config.json',
        'content' => base64_encode('{"key": "value"}'),
        'encoding' => 'base64',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/config.json', ['ref' => 'main'])
        ->andReturn($response);

    $content = $this->query->ref('main')->get('config.json');

    expect($content->name)->toBe('config.json');
});

it('returns empty collection when no items match type filter', function () {
    $responseData = [
        [
            'name' => 'subfolder',
            'path' => 'src/subfolder',
            'sha' => 'ghi789',
            'size' => 0,
            'type' => 'dir',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/subfolder',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src', [])
        ->andReturn($response);

    $files = $this->query->whereType('file')->list('src');

    expect($files)->toBeEmpty();
});

it('returns empty collection when no items match extension filter', function () {
    $responseData = [
        [
            'name' => 'file1.js',
            'path' => 'src/file1.js',
            'sha' => 'abc123',
            'size' => 512,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/file1.js',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src', [])
        ->andReturn($response);

    $phpFiles = $this->query->whereExtension('php')->list('src');

    expect($phpFiles)->toBeEmpty();
});

it('can chain multiple filters', function () {
    $responseData = [
        [
            'name' => 'file1.php',
            'path' => 'src/file1.php',
            'sha' => 'abc123',
            'size' => 512,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/file1.php',
        ],
        [
            'name' => 'file2.js',
            'path' => 'src/file2.js',
            'sha' => 'def456',
            'size' => 256,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/file2.js',
        ],
        [
            'name' => 'subfolder',
            'path' => 'src/subfolder',
            'sha' => 'ghi789',
            'size' => 0,
            'type' => 'dir',
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/subfolder',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src', [])
        ->andReturn($response);

    $phpFiles = $this->query->whereType('file')->whereExtension('php')->list('src');

    expect($phpFiles)->toHaveCount(1);
    expect($phpFiles->first()->name)->toBe('file1.php');
});

it('can create a file with author and committer', function () {
    $requestData = [
        'message' => 'Add file',
        'content' => base64_encode('Content'),
        'author' => [
            'name' => 'Author Name',
            'email' => 'author@example.com',
        ],
        'committer' => [
            'name' => 'Committer Name',
            'email' => 'committer@example.com',
        ],
    ];

    $responseData = [
        'content' => [
            'name' => 'file.txt',
            'path' => 'file.txt',
            'sha' => 'abc123',
            'size' => 7,
            'type' => 'file',
            'url' => 'https://api.github.com/repos/owner/repo/contents/file.txt',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/file.txt', $requestData)
        ->andReturn($response);

    $result = $this->query->create('file.txt', [
        'content' => 'Content',
        'message' => 'Add file',
        'author' => [
            'name' => 'Author Name',
            'email' => 'author@example.com',
        ],
        'committer' => [
            'name' => 'Committer Name',
            'email' => 'committer@example.com',
        ],
    ]);

    expect($result['content']['name'])->toBe('file.txt');
});
