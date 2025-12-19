<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

final readonly class Collaborator
{
    public function __construct(
        public int $id,
        public string $login,
        public string $avatarUrl,
        public string $htmlUrl,
        public Permissions $permissions,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            login: $data['login'],
            avatarUrl: $data['avatar_url'],
            htmlUrl: $data['html_url'],
            permissions: Permissions::fromArray($data['permissions'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'avatar_url' => $this->avatarUrl,
            'html_url' => $this->htmlUrl,
            'permissions' => $this->permissions->toArray(),
        ];
    }
}
