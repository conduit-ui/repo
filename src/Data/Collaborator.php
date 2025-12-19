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

    public function isAdmin(): bool
    {
        return $this->permissions->admin;
    }

    public function canWrite(): bool
    {
        return $this->permissions->push || $this->permissions->admin || $this->permissions->maintain;
    }

    public function canRead(): bool
    {
        return $this->permissions->pull;
    }

    public function hasPermission(string $permission): bool
    {
        return match ($permission) {
            'admin' => $this->permissions->admin,
            'maintain' => $this->permissions->maintain,
            'push' => $this->permissions->push,
            'triage' => $this->permissions->triage,
            'pull' => $this->permissions->pull,
            default => false,
        };
    }

    public function getPermissionLevel(): string
    {
        if ($this->permissions->admin) {
            return 'admin';
        }

        if ($this->permissions->maintain) {
            return 'maintain';
        }

        if ($this->permissions->push) {
            return 'push';
        }

        if ($this->permissions->triage) {
            return 'triage';
        }

        return 'pull';
    }
}
