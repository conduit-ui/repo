<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\FileQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new FileQuery($this->connector, 'owner', 'repo');
});

afterEach(function () {
    m::close();
});

it('can get file content', function () {
    $responseData = [
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

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src/example.php', [])
        ->andReturn($response);

    $file = $this->query->path('src/example.php')->get();

    expect($file->name)->toBe('example.php');
    expect($file->path)->toBe('src/example.php');
    expect($file->sha)->toBe('abc123def456');
});

it('can get file content with ref parameter', function () {
    $responseData = [
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

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src/example.php', ['ref' => 'develop'])
        ->andReturn($response);

    $file = $this->query->path('src/example.php')->ref('develop')->get();

    expect($file->name)->toBe('example.php');
});

it('can get file content with branch parameter', function () {
    $responseData = [
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

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/contents/src/example.php', ['ref' => 'feature'])
        ->andReturn($response);

    $file = $this->query->path('src/example.php')->branch('feature')->get();

    expect($file->name)->toBe('example.php');
});

it('throws exception when path is not set', function () {
    $this->query->get();
})->throws(InvalidArgumentException::class, 'File path is required');

it('can create a new file', function () {
    $content = '<?php echo "New File";';
    $message = 'Create new file';

    $responseData = [
        'content' => [
            'name' => 'new-file.php',
            'path' => 'src/new-file.php',
            'sha' => 'xyz789',
            'size' => 100,
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/new-file.php',
            'html_url' => 'https://github.com/owner/repo/blob/main/src/new-file.php',
            'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/xyz789',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/new-file.php',
            'type' => 'file',
            'content' => base64_encode($content),
            'encoding' => 'base64',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/src/new-file.php', [
            'message' => $message,
            'content' => base64_encode($content),
        ])
        ->andReturn($response);

    $file = $this->query->create('src/new-file.php', $content, $message);

    expect($file->name)->toBe('new-file.php');
    expect($file->path)->toBe('src/new-file.php');
});

it('can create a new file on specific branch', function () {
    $content = '<?php echo "New File";';
    $message = 'Create new file';
    $branch = 'feature';

    $responseData = [
        'content' => [
            'name' => 'new-file.php',
            'path' => 'src/new-file.php',
            'sha' => 'xyz789',
            'size' => 100,
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/new-file.php',
            'html_url' => 'https://github.com/owner/repo/blob/main/src/new-file.php',
            'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/xyz789',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/new-file.php',
            'type' => 'file',
            'content' => base64_encode($content),
            'encoding' => 'base64',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/src/new-file.php', [
            'message' => $message,
            'content' => base64_encode($content),
            'branch' => $branch,
        ])
        ->andReturn($response);

    $file = $this->query->create('src/new-file.php', $content, $message, $branch);

    expect($file->name)->toBe('new-file.php');
});

it('can update an existing file', function () {
    $content = '<?php echo "Updated Content";';
    $message = 'Update existing file';
    $sha = 'abc123def456';

    $responseData = [
        'content' => [
            'name' => 'example.php',
            'path' => 'src/example.php',
            'sha' => 'newsha789',
            'size' => 150,
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/example.php',
            'html_url' => 'https://github.com/owner/repo/blob/main/src/example.php',
            'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/newsha789',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/example.php',
            'type' => 'file',
            'content' => base64_encode($content),
            'encoding' => 'base64',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/src/example.php', [
            'message' => $message,
            'content' => base64_encode($content),
            'sha' => $sha,
        ])
        ->andReturn($response);

    $file = $this->query->update('src/example.php', $content, $message, $sha);

    expect($file->name)->toBe('example.php');
    expect($file->sha)->toBe('newsha789');
});

it('can update an existing file on specific branch', function () {
    $content = '<?php echo "Updated Content";';
    $message = 'Update existing file';
    $sha = 'abc123def456';
    $branch = 'develop';

    $responseData = [
        'content' => [
            'name' => 'example.php',
            'path' => 'src/example.php',
            'sha' => 'newsha789',
            'size' => 150,
            'url' => 'https://api.github.com/repos/owner/repo/contents/src/example.php',
            'html_url' => 'https://github.com/owner/repo/blob/main/src/example.php',
            'git_url' => 'https://api.github.com/repos/owner/repo/git/blobs/newsha789',
            'download_url' => 'https://raw.githubusercontent.com/owner/repo/main/src/example.php',
            'type' => 'file',
            'content' => base64_encode($content),
            'encoding' => 'base64',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('put')
        ->with('/repos/owner/repo/contents/src/example.php', [
            'message' => $message,
            'content' => base64_encode($content),
            'sha' => $sha,
            'branch' => $branch,
        ])
        ->andReturn($response);

    $file = $this->query->update('src/example.php', $content, $message, $sha, $branch);

    expect($file->name)->toBe('example.php');
});

it('can delete a file', function () {
    $message = 'Delete file';
    $sha = 'abc123def456';

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/contents/src/example.php', [
            'message' => $message,
            'sha' => $sha,
        ])
        ->andReturn($response);

    $result = $this->query->delete('src/example.php', $message, $sha);

    expect($result)->toBeTrue();
});

it('can delete a file on specific branch', function () {
    $message = 'Delete file';
    $sha = 'abc123def456';
    $branch = 'feature';

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/contents/src/example.php', [
            'message' => $message,
            'sha' => $sha,
            'branch' => $branch,
        ])
        ->andReturn($response);

    $result = $this->query->delete('src/example.php', $message, $sha, $branch);

    expect($result)->toBeTrue();
});

it('returns false when delete fails', function () {
    $message = 'Delete file';
    $sha = 'abc123def456';

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/contents/src/example.php', [
            'message' => $message,
            'sha' => $sha,
        ])
        ->andReturn($response);

    $result = $this->query->delete('src/example.php', $message, $sha);

    expect($result)->toBeFalse();
});
