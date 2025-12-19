<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Webhook;

it('can create webhook from array', function () {
    $data = [
        'id' => 1,
        'type' => 'Repository',
        'name' => 'web',
        'active' => true,
        'events' => ['push', 'pull_request'],
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
            'secret' => 'my-secret',
        ],
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
        'url' => 'https://api.github.com/repos/owner/repo/hooks/1',
    ];

    $webhook = Webhook::fromArray($data);

    expect($webhook->id)->toBe(1);
    expect($webhook->type)->toBe('Repository');
    expect($webhook->name)->toBe('web');
    expect($webhook->active)->toBeTrue();
    expect($webhook->events)->toContain('push');
});

it('can convert webhook to array', function () {
    $data = [
        'id' => 2,
        'type' => 'Repository',
        'name' => 'web',
        'active' => false,
        'events' => ['issues'],
        'config' => ['url' => 'https://example.com/webhook'],
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
    ];

    $webhook = Webhook::fromArray($data);
    $array = $webhook->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe(2);
    expect($array['active'])->toBeFalse();
});

it('can check if webhook is active', function () {
    $webhook = Webhook::fromArray([
        'id' => 3,
        'active' => true,
        'events' => ['push'],
        'config' => [],
    ]);

    expect($webhook->isActive())->toBeTrue();
});

it('can check if webhook has specific event', function () {
    $webhook = Webhook::fromArray([
        'id' => 4,
        'events' => ['push', 'pull_request'],
        'config' => [],
    ]);

    expect($webhook->hasEvent('push'))->toBeTrue();
    expect($webhook->hasEvent('issues'))->toBeFalse();
});

it('can get webhook url from config', function () {
    $webhook = Webhook::fromArray([
        'id' => 5,
        'events' => [],
        'config' => ['url' => 'https://example.com/webhook'],
    ]);

    expect($webhook->getUrl())->toBe('https://example.com/webhook');
});

it('can get content type from config', function () {
    $webhook = Webhook::fromArray([
        'id' => 6,
        'events' => [],
        'config' => ['content_type' => 'json'],
    ]);

    expect($webhook->getContentType())->toBe('json');
});

it('can check if webhook has secret', function () {
    $webhookWithSecret = Webhook::fromArray([
        'id' => 7,
        'events' => [],
        'config' => ['secret' => 'my-secret'],
    ]);

    $webhookWithoutSecret = Webhook::fromArray([
        'id' => 8,
        'events' => [],
        'config' => [],
    ]);

    expect($webhookWithSecret->hasSecret())->toBeTrue();
    expect($webhookWithoutSecret->hasSecret())->toBeFalse();
});
