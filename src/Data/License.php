<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class License
{
    public function __construct(
        public string $key,
        public string $name,
        public string $spdxId,
        public ?string $url = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'],
            name: $data['name'],
            spdxId: $data['spdx_id'],
            url: $data['url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'spdx_id' => $this->spdxId,
            'url' => $this->url,
        ];
    }
}
