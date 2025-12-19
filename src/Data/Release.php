<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

use ConduitUi\GitHubConnector\Connector;
use DateTimeImmutable;

final class Release
{
    protected array $pendingChanges = [];

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

    // Chainable action methods

    public function publish(): self
    {
        return $this->updateAttributes(['draft' => false]);
    }

    public function markAsDraft(): self
    {
        return $this->updateAttributes(['draft' => true]);
    }

    public function markAsPrerelease(): self
    {
        return $this->updateAttributes(['prerelease' => true]);
    }

    public function markAsLatest(): self
    {
        return $this->updateAttributes(['make_latest' => 'true']);
    }

    public function rename(string $newName): self
    {
        return $this->updateAttributes(['name' => $newName]);
    }

    public function updateBody(string $body): self
    {
        return $this->updateAttributes(['body' => $body]);
    }

    public function update(array $attributes): self
    {
        return $this->updateAttributes($attributes);
    }

    // Persistence methods

    public function delete(): bool
    {
        $connector = app(Connector::class);
        $response = $connector->delete("/repos/*/releases/{$this->id}");

        return $response->successful();
    }

    public function refresh(): self
    {
        $connector = app(Connector::class);
        $response = $connector->get("/repos/*/releases/{$this->id}");

        return self::fromArray($response->json());
    }

    // Asset management methods

    public function uploadAsset(string $filePath, string $fileName, string $contentType): ReleaseAsset
    {
        $connector = app(Connector::class);
        $response = $connector->upload("/repos/*/releases/{$this->id}/assets", $filePath, $fileName, $contentType);

        return ReleaseAsset::fromArray($response->json());
    }

    public function deleteAsset(int $assetId): bool
    {
        $connector = app(Connector::class);
        $response = $connector->delete("/repos/*/releases/assets/{$assetId}");

        return $response->successful();
    }

    // Helper methods

    public function isDraft(): bool
    {
        return $this->draft;
    }

    public function isPrerelease(): bool
    {
        return $this->prerelease;
    }

    public function isPublished(): bool
    {
        return ! $this->draft && $this->publishedAt !== null;
    }

    // Internal methods

    protected function updateAttributes(array $attributes): self
    {
        $connector = app(Connector::class);
        $response = $connector->patch("/repos/*/releases/{$this->id}", $attributes);

        return self::fromArray($response->json());
    }
}
