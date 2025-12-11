<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class Permissions
{
    public function __construct(
        public bool $admin,
        public bool $maintain,
        public bool $push,
        public bool $triage,
        public bool $pull,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            admin: $data['admin'] ?? false,
            maintain: $data['maintain'] ?? false,
            push: $data['push'] ?? false,
            triage: $data['triage'] ?? false,
            pull: $data['pull'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'admin' => $this->admin,
            'maintain' => $this->maintain,
            'push' => $this->push,
            'triage' => $this->triage,
            'pull' => $this->pull,
        ];
    }
}
