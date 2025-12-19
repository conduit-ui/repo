<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Webhook;
use ConduitUI\Repos\Services\WebhookQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new WebhookQuery($this->connector, 'owner', 'repo');
});

afterEach(function () {
    m::close();
});

it('can get all webhooks', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'web',
            'events' => ['push'],
            'active' => true,
            'config' => [
                'url' => 'https://example.com/webhook',
                'content_type' => 'json',
            ],
        ],
        [
            'id' => 2,
            'name' => 'web',
            'events' => ['pull_request'],
            'active' => true,
            'config' => [
                'url' => 'https://example.com/webhook2',
                'content_type' => 'json',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/hooks')
        ->andReturn($response);

    $webhooks = $this->query->get();

    expect($webhooks)->toHaveCount(2);
    expect($webhooks->first())->toBeInstanceOf(Webhook::class);
    expect($webhooks->first()->id)->toBe(1);
    expect($webhooks->last()->id)->toBe(2);
});

it('can filter webhooks by active status', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'web',
            'events' => ['push'],
            'active' => true,
            'config' => ['url' => 'https://example.com/webhook'],
        ],
        [
            'id' => 2,
            'name' => 'web',
            'events' => ['pull_request'],
            'active' => false,
            'config' => ['url' => 'https://example.com/webhook2'],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/hooks')
        ->andReturn($response);

    $webhooks = $this->query->whereActive()->get();

    expect($webhooks)->toHaveCount(1);
    expect($webhooks->first()->active)->toBeTrue();
});

it('can filter webhooks by inactive status', function () {
    $responseData = [
        [
            'id' => 1,
            'name' => 'web',
            'events' => ['push'],
            'active' => true,
            'config' => ['url' => 'https://example.com/webhook'],
        ],
        [
            'id' => 2,
            'name' => 'web',
            'events' => ['pull_request'],
            'active' => false,
            'config' => ['url' => 'https://example.com/webhook2'],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/hooks')
        ->andReturn($response);

    $webhooks = $this->query->whereInactive()->get();

    expect($webhooks)->toHaveCount(1);
    expect($webhooks->first()->active)->toBeFalse();
});

it('can find a specific webhook by id', function () {
    $responseData = [
        'id' => 1,
        'name' => 'web',
        'events' => ['push'],
        'active' => true,
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/hooks/1')
        ->andReturn($response);

    $webhook = $this->query->find(1);

    expect($webhook)->toBeInstanceOf(Webhook::class);
    expect($webhook->id)->toBe(1);
    expect($webhook->url)->toBe('https://example.com/webhook');
});

it('can create a webhook', function () {
    $config = [
        'url' => 'https://example.com/webhook',
        'events' => ['push', 'pull_request'],
        'active' => true,
    ];

    $responseData = [
        'id' => 1,
        'name' => 'web',
        'events' => ['push', 'pull_request'],
        'active' => true,
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/hooks', m::subset($config))
        ->andReturn($response);

    $webhook = $this->query->create($config);

    expect($webhook)->toBeInstanceOf(Webhook::class);
    expect($webhook->id)->toBe(1);
    expect($webhook->events)->toBe(['push', 'pull_request']);
});

it('can update a webhook', function () {
    $config = [
        'events' => ['push', 'release'],
        'active' => false,
    ];

    $responseData = [
        'id' => 1,
        'name' => 'web',
        'events' => ['push', 'release'],
        'active' => false,
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('patch')
        ->with('/repos/owner/repo/hooks/1', $config)
        ->andReturn($response);

    $webhook = $this->query->update(1, $config);

    expect($webhook)->toBeInstanceOf(Webhook::class);
    expect($webhook->active)->toBeFalse();
    expect($webhook->events)->toBe(['push', 'release']);
});

it('can delete a webhook', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('delete')
        ->with('/repos/owner/repo/hooks/1')
        ->andReturn($response);

    $result = $this->query->delete(1);

    expect($result)->toBeTrue();
});

it('can ping a webhook', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/hooks/1/pings', [])
        ->andReturn($response);

    $result = $this->query->ping(1);

    expect($result)->toBeTrue();
});
