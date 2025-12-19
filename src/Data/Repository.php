<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

use ConduitUI\Repos\Contracts\RepositoryDataContract;
use DateTimeImmutable;

final readonly class Repository implements RepositoryDataContract
{
    public function __construct(
        public int $id,
        public string $name,
        public string $fullName,
        public string $description,
        public string $visibility,
        public string $defaultBranch,
        public bool $private,
        public bool $fork,
        public bool $archived,
        public bool $disabled,
        public ?string $language,
        public int $stargazersCount,
        public int $watchersCount,
        public int $forksCount,
        public int $openIssuesCount,
        public ?string $homepage,
        public string $htmlUrl,
        public string $cloneUrl,
        public string $sshUrl,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $pushedAt,
        public int $size,
        public ?Owner $owner = null,
        public ?License $license = null,
        public array $topics = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            fullName: $data['full_name'],
            description: $data['description'] ?? '',
            visibility: $data['visibility'] ?? 'public',
            defaultBranch: $data['default_branch'] ?? 'main',
            private: $data['private'] ?? false,
            fork: $data['fork'] ?? false,
            archived: $data['archived'] ?? false,
            disabled: $data['disabled'] ?? false,
            language: $data['language'] ?? null,
            stargazersCount: $data['stargazers_count'] ?? 0,
            watchersCount: $data['watchers_count'] ?? 0,
            forksCount: $data['forks_count'] ?? 0,
            openIssuesCount: $data['open_issues_count'] ?? 0,
            homepage: $data['homepage'] ?? null,
            htmlUrl: $data['html_url'],
            cloneUrl: $data['clone_url'],
            sshUrl: $data['ssh_url'],
            createdAt: isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null,
            pushedAt: isset($data['pushed_at']) ? new DateTimeImmutable($data['pushed_at']) : null,
            size: $data['size'] ?? 0,
            owner: isset($data['owner']) ? Owner::fromArray($data['owner']) : null,
            license: isset($data['license']) ? License::fromArray($data['license']) : null,
            topics: $data['topics'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'full_name' => $this->fullName,
            'description' => $this->description,
            'visibility' => $this->visibility,
            'default_branch' => $this->defaultBranch,
            'private' => $this->private,
            'fork' => $this->fork,
            'archived' => $this->archived,
            'disabled' => $this->disabled,
            'language' => $this->language,
            'stargazers_count' => $this->stargazersCount,
            'watchers_count' => $this->watchersCount,
            'forks_count' => $this->forksCount,
            'open_issues_count' => $this->openIssuesCount,
            'homepage' => $this->homepage,
            'html_url' => $this->htmlUrl,
            'clone_url' => $this->cloneUrl,
            'ssh_url' => $this->sshUrl,
            'created_at' => $this->createdAt?->format('c'),
            'updated_at' => $this->updatedAt?->format('c'),
            'pushed_at' => $this->pushedAt?->format('c'),
            'size' => $this->size,
            'owner' => $this->owner?->toArray(),
            'license' => $this->license?->toArray(),
            'topics' => $this->topics,
        ];
    }
}
