<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\WebhookQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new WebhookQuery($this->connector, 'owner/repo');
});

afterEach(function () {
    m::close();
});

it('can list webhooks for a repository', function () {
    $responseData = [
        [
            'id' => 1,
            'type' => 'Repository',
            'name' => 'web',
            'active' => true,
            'events' => ['push', 'pull_request'],
            'config' => [
                'url' => 'https://example.com/webhook',
                'content_type' => 'json',
                'insecure_ssl' => '0',
            ],
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-02T00:00:00Z',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/hooks', [
            'per_page' => 30,
            'page' => 1,
        ])
        ->andReturn($response);

    $webhooks = $this->query->get();

    expect($webhooks)->toHaveCount(1);
    expect($webhooks->first()->id)->toBe(1);
    expect($webhooks->first()->active)->toBeTrue();
});

it('can create a webhook', function () {
    $webhookData = [
        'name' => 'web',
        'active' => true,
        'events' => ['push'],
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
        ],
    ];

    $responseData = array_merge($webhookData, [
        'id' => 2,
        'type' => 'Repository',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
    ]);

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/hooks', $webhookData)
        ->andReturn($response);

    $webhook = $this->query->create(
        url: 'https://example.com/webhook',
        events: ['push'],
        contentType: 'json',
        active: true,
    );

    expect($webhook->id)->toBe(2);
    expect($webhook->config['url'])->toBe('https://example.com/webhook');
});

it('can update a webhook', function () {
    $updateData = [
        'active' => false,
        'events' => ['push', 'pull_request'],
    ];

    $responseData = [
        'id' => 3,
        'type' => 'Repository',
        'name' => 'web',
        'active' => false,
        'events' => ['push', 'pull_request'],
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
        ],
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-03T00:00:00Z',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('patch')
        ->with('/repos/owner/repo/hooks/3', $updateData)
        ->andReturn($response);

    $webhook = $this->query->update(3, $updateData);

    expect($webhook->id)->toBe(3);
    expect($webhook->active)->toBeFalse();
    expect($webhook->events)->toContain('pull_request');
});

it('can delete a webhook', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/hooks/4')
        ->andReturn($response);

    $result = $this->query->delete(4);

    expect($result)->toBeTrue();
});

it('can get a specific webhook', function () {
    $responseData = [
        'id' => 5,
        'type' => 'Repository',
        'name' => 'web',
        'active' => true,
        'events' => ['push'],
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
        ],
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/hooks/5')
        ->andReturn($response);

    $webhook = $this->query->find(5);

    expect($webhook->id)->toBe(5);
    expect($webhook->events)->toContain('push');
});

it('can ping a webhook', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/hooks/6/pings', [])
        ->andReturn($response);

    $result = $this->query->ping(6);

    expect($result)->toBeTrue();
});

it('can test a webhook', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/hooks/7/tests', [])
        ->andReturn($response);

    $result = $this->query->test(7);

    expect($result)->toBeTrue();
});

it('can filter webhooks by active status', function () {
    $responseData = [
        [
            'id' => 8,
            'type' => 'Repository',
            'name' => 'web',
            'active' => true,
            'events' => ['push'],
            'config' => ['url' => 'https://example.com/webhook1'],
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-02T00:00:00Z',
        ],
        [
            'id' => 9,
            'type' => 'Repository',
            'name' => 'web',
            'active' => false,
            'events' => ['push'],
            'config' => ['url' => 'https://example.com/webhook2'],
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-02T00:00:00Z',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->andReturn($response);

    $webhooks = $this->query->whereActive(true)->get();

    expect($webhooks)->toHaveCount(1);
    expect($webhooks->first()->active)->toBeTrue();
});

it('can filter webhooks by event type', function () {
    $responseData = [
        [
            'id' => 10,
            'type' => 'Repository',
            'name' => 'web',
            'active' => true,
            'events' => ['push', 'pull_request'],
            'config' => ['url' => 'https://example.com/webhook1'],
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-02T00:00:00Z',
        ],
        [
            'id' => 11,
            'type' => 'Repository',
            'name' => 'web',
            'active' => true,
            'events' => ['issues'],
            'config' => ['url' => 'https://example.com/webhook2'],
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-02T00:00:00Z',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->andReturn($response);

    $webhooks = $this->query->whereEvent('pull_request')->get();

    expect($webhooks)->toHaveCount(1);
    expect($webhooks->first()->events)->toContain('pull_request');
});

it('can set pagination parameters', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn([]);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/hooks', [
            'per_page' => 50,
            'page' => 2,
        ])
        ->andReturn($response);

    $result = $this->query->perPage(50)->page(2);

    expect($result)->toBeInstanceOf(WebhookQuery::class);
    $result->get();
});

it('can create webhook with secret', function () {
    $webhookData = [
        'name' => 'web',
        'active' => true,
        'events' => ['push'],
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
            'secret' => 'my-secret-token',
        ],
    ];

    $responseData = array_merge($webhookData, [
        'id' => 12,
        'type' => 'Repository',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
    ]);

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/hooks', $webhookData)
        ->andReturn($response);

    $webhook = $this->query->create(
        url: 'https://example.com/webhook',
        events: ['push'],
        secret: 'my-secret-token',
    );

    expect($webhook->id)->toBe(12);
    expect($webhook->config['secret'])->toBe('my-secret-token');
});

it('can create webhook with insecure SSL option', function () {
    $webhookData = [
        'name' => 'web',
        'active' => true,
        'events' => ['push'],
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
            'insecure_ssl' => '1',
        ],
    ];

    $responseData = array_merge($webhookData, [
        'id' => 13,
        'type' => 'Repository',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
    ]);

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/hooks', $webhookData)
        ->andReturn($response);

    $webhook = $this->query->create(
        url: 'https://example.com/webhook',
        events: ['push'],
        insecureSsl: true,
    );

    expect($webhook->id)->toBe(13);
    expect($webhook->config['insecure_ssl'])->toBe('1');
});
