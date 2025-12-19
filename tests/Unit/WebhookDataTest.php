<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Webhook;

it('can create webhook from array', function () {
    $data = [
        'id' => 1,
        'name' => 'web',
        'events' => ['push', 'pull_request'],
        'active' => true,
        'type' => 'Repository',
        'config' => [
            'url' => 'https://example.com/webhook',
            'content_type' => 'json',
        ],
        'created_at' => '2024-01-01T00:00:00Z',
        'updated_at' => '2024-01-02T00:00:00Z',
    ];

    $webhook = Webhook::fromArray($data);

    expect($webhook->id)->toBe(1);
    expect($webhook->name)->toBe('web');
    expect($webhook->url)->toBe('https://example.com/webhook');
    expect($webhook->events)->toBe(['push', 'pull_request']);
    expect($webhook->active)->toBeTrue();
    expect($webhook->type)->toBe('Repository');
    expect($webhook->createdAt)->toBe('2024-01-01T00:00:00Z');
    expect($webhook->updatedAt)->toBe('2024-01-02T00:00:00Z');
});

it('can create webhook from array with minimal data', function () {
    $data = [
        'id' => 1,
        'events' => ['push'],
        'config' => [
            'url' => 'https://example.com/webhook',
        ],
    ];

    $webhook = Webhook::fromArray($data);

    expect($webhook->id)->toBe(1);
    expect($webhook->name)->toBe('web');
    expect($webhook->url)->toBe('https://example.com/webhook');
    expect($webhook->events)->toBe(['push']);
    expect($webhook->active)->toBeTrue();
});

it('can convert webhook to array', function () {
    $webhook = new Webhook(
        id: 1,
        name: 'web',
        url: 'https://example.com/webhook',
        events: ['push'],
        active: true,
        type: 'Repository',
        config: ['url' => 'https://example.com/webhook'],
        createdAt: '2024-01-01T00:00:00Z',
        updatedAt: '2024-01-02T00:00:00Z',
    );

    $array = $webhook->toArray();

    expect($array)->toMatchArray([
        'id' => 1,
        'name' => 'web',
        'url' => 'https://example.com/webhook',
        'events' => ['push'],
        'active' => true,
        'type' => 'Repository',
        'created_at' => '2024-01-01T00:00:00Z',
        'updated_at' => '2024-01-02T00:00:00Z',
    ]);
});

it('handles missing url field', function () {
    $data = [
        'id' => 1,
        'name' => 'web',
        'events' => ['push'],
        'active' => true,
    ];

    $webhook = Webhook::fromArray($data);

    expect($webhook->url)->toBe('');
});
