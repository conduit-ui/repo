<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class Workflow
{
    public function __construct(
        public int $id,
        public string $name,
        public string $path,
        public string $state,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $url = null,
        public ?string $htmlUrl = null,
        public ?string $badgeUrl = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            path: $data['path'],
            state: $data['state'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            url: $data['url'] ?? null,
            htmlUrl: $data['html_url'] ?? null,
            badgeUrl: $data['badge_url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'state' => $this->state,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'url' => $this->url,
            'html_url' => $this->htmlUrl,
            'badge_url' => $this->badgeUrl,
        ];
    }
}
