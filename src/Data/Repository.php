<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

use ConduitUI\Repos\Contracts\RepositoryDataContract;
use ConduitUI\Repos\Services\Repositories;
use DateTimeImmutable;

final class Repository implements RepositoryDataContract
{
    protected array $pendingChanges = [];

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

    // Chainable action methods

    public function archive(): self
    {
        return $this->addPendingChange('archived', true);
    }

    public function unarchive(): self
    {
        return $this->addPendingChange('archived', false);
    }

    public function makePrivate(): self
    {
        return $this->addPendingChange('private', true);
    }

    public function makePublic(): self
    {
        return $this->addPendingChange('private', false);
    }

    public function transfer(string $newOwner): self
    {
        return $this->addPendingChange('new_owner', $newOwner);
    }

    public function rename(string $newName): self
    {
        return $this->addPendingChange('name', $newName);
    }

    public function enableWiki(): self
    {
        return $this->addPendingChange('has_wiki', true);
    }

    public function disableWiki(): self
    {
        return $this->addPendingChange('has_wiki', false);
    }

    public function enableIssues(): self
    {
        return $this->addPendingChange('has_issues', true);
    }

    public function disableIssues(): self
    {
        return $this->addPendingChange('has_issues', false);
    }

    public function enableProjects(): self
    {
        return $this->addPendingChange('has_projects', true);
    }

    public function disableProjects(): self
    {
        return $this->addPendingChange('has_projects', false);
    }

    public function enableDiscussions(): self
    {
        return $this->addPendingChange('has_discussions', true);
    }

    public function disableDiscussions(): self
    {
        return $this->addPendingChange('has_discussions', false);
    }

    public function setTopics(array $topics): self
    {
        return $this->addPendingChange('topics', $topics);
    }

    public function addTopic(string $topic): self
    {
        $currentTopics = $this->pendingChanges['topics'] ?? $this->topics;
        $newTopics = array_unique([...$currentTopics, $topic]);

        return $this->addPendingChange('topics', $newTopics);
    }

    public function removeTopic(string $topic): self
    {
        $currentTopics = $this->pendingChanges['topics'] ?? $this->topics;
        $newTopics = array_values(array_filter($currentTopics, fn ($t) => $t !== $topic));

        return $this->addPendingChange('topics', $newTopics);
    }

    public function setDefaultBranch(string $branch): self
    {
        return $this->addPendingChange('default_branch', $branch);
    }

    public function update(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->addPendingChange($key, $value);
        }

        return $this;
    }

    // Persistence methods

    public function save(): self
    {
        if (empty($this->pendingChanges)) {
            return $this;
        }

        /** @var Repository $updated */
        $updated = app(Repositories::class)
            ->update($this->fullName, $this->pendingChanges);

        $this->pendingChanges = [];

        return $updated;
    }

    public function refresh(): self
    {
        /** @var Repository $refreshed */
        $refreshed = app(Repositories::class)->find($this->fullName);

        return $refreshed;
    }

    public function delete(): bool
    {
        return app(Repositories::class)->delete($this->fullName);
    }

    // Helper methods

    public function isPublic(): bool
    {
        return ! $this->private;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function isFork(): bool
    {
        return $this->fork;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    // Internal state management

    protected function addPendingChange(string $key, mixed $value): self
    {
        $this->pendingChanges[$key] = $value;

        return $this;
    }
}
