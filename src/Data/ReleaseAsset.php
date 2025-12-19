<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class ReleaseAsset
{
    public function __construct(
        public int $id,
        public string $name,
        public string $contentType,
        public int $size,
        public int $downloadCount,
        public string $browserDownloadUrl,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            contentType: $data['content_type'],
            size: $data['size'],
            downloadCount: $data['download_count'] ?? 0,
            browserDownloadUrl: $data['browser_download_url'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content_type' => $this->contentType,
            'size' => $this->size,
            'download_count' => $this->downloadCount,
            'browser_download_url' => $this->browserDownloadUrl,
        ];
    }
}
