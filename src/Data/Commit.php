<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class Commit
{
    public function __construct(
        public string $sha,
        public string $url,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            sha: $data['sha'],
            url: $data['url'],
        );
    }

    public function toArray(): array
    {
        return [
            'sha' => $this->sha,
            'url' => $this->url,
        ];
    }
}
