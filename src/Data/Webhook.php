<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

use DateTimeImmutable;

final class Webhook
{
    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public bool $active,
        public array $events,
        public array $config,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null,
        public ?string $url = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            type: $data['type'] ?? 'Repository',
            name: $data['name'] ?? 'web',
            active: $data['active'] ?? true,
            events: $data['events'] ?? [],
            config: $data['config'] ?? [],
            createdAt: isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null,
            url: $data['url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'active' => $this->active,
            'events' => $this->events,
            'config' => $this->config,
            'created_at' => $this->createdAt?->format('c'),
            'updated_at' => $this->updatedAt?->format('c'),
            'url' => $this->url,
        ];
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function hasEvent(string $event): bool
    {
        return in_array($event, $this->events, true);
    }

    public function getUrl(): ?string
    {
        return $this->config['url'] ?? null;
    }

    public function getContentType(): ?string
    {
        return $this->config['content_type'] ?? null;
    }

    public function hasSecret(): bool
    {
        return isset($this->config['secret']);
    }
}
