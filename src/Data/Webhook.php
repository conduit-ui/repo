<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class Webhook
{
    public function __construct(
        public int $id,
        public string $name,
        public string $url,
        public array $events,
        public bool $active,
        public ?string $type = null,
        public ?array $config = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'] ?? 'web',
            url: $data['config']['url'] ?? $data['url'] ?? '',
            events: $data['events'] ?? [],
            active: $data['active'] ?? true,
            type: $data['type'] ?? null,
            config: $data['config'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'events' => $this->events,
            'active' => $this->active,
            'type' => $this->type,
            'config' => $this->config,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
