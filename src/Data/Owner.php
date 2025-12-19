<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class Owner
{
    public function __construct(
        public int $id,
        public string $login,
        public string $type,
        public string $avatarUrl,
        public string $htmlUrl,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            login: $data['login'],
            type: $data['type'] ?? 'User',
            avatarUrl: $data['avatar_url'],
            htmlUrl: $data['html_url'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'type' => $this->type,
            'avatar_url' => $this->avatarUrl,
            'html_url' => $this->htmlUrl,
        ];
    }
}
