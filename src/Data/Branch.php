<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class Branch
{
    public function __construct(
        public string $name,
        public bool $protected,
        public Commit $commit,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            protected: $data['protected'] ?? false,
            commit: Commit::fromArray($data['commit']),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'protected' => $this->protected,
            'commit' => $this->commit->toArray(),
        ];
    }
}
