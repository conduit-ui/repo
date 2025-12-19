<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

use DateTimeImmutable;

final readonly class Release
{
    public function __construct(
        public int $id,
        public string $tagName,
        public string $name,
        public string $body,
        public bool $draft,
        public bool $prerelease,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $publishedAt,
        public string $htmlUrl,
        public array $assets = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            tagName: $data['tag_name'],
            name: $data['name'] ?? '',
            body: $data['body'] ?? '',
            draft: $data['draft'] ?? false,
            prerelease: $data['prerelease'] ?? false,
            createdAt: isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : null,
            publishedAt: isset($data['published_at']) ? new DateTimeImmutable($data['published_at']) : null,
            htmlUrl: $data['html_url'],
            assets: array_map(fn ($asset) => ReleaseAsset::fromArray($asset), $data['assets'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tag_name' => $this->tagName,
            'name' => $this->name,
            'body' => $this->body,
            'draft' => $this->draft,
            'prerelease' => $this->prerelease,
            'created_at' => $this->createdAt?->format('c'),
            'published_at' => $this->publishedAt?->format('c'),
            'html_url' => $this->htmlUrl,
            'assets' => array_map(fn ($asset) => $asset->toArray(), $this->assets),
        ];
    }
}
