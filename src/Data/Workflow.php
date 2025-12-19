<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

use DateTimeImmutable;

final class Workflow
{
    public function __construct(
        public int $id,
        public string $nodeId,
        public string $name,
        public string $path,
        public string $state,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null,
        public ?string $url = null,
        public ?string $htmlUrl = null,
        public ?string $badgeUrl = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            nodeId: $data['node_id'],
            name: $data['name'],
            path: $data['path'],
            state: $data['state'] ?? 'active',
            createdAt: isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null,
            url: $data['url'] ?? null,
            htmlUrl: $data['html_url'] ?? null,
            badgeUrl: $data['badge_url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'node_id' => $this->nodeId,
            'name' => $this->name,
            'path' => $this->path,
            'state' => $this->state,
            'created_at' => $this->createdAt?->format('c'),
            'updated_at' => $this->updatedAt?->format('c'),
            'url' => $this->url,
            'html_url' => $this->htmlUrl,
            'badge_url' => $this->badgeUrl,
        ];
    }

    public function isActive(): bool
    {
        return $this->state === 'active';
    }

    public function isDisabled(): bool
    {
        return $this->state === 'disabled';
    }
}
