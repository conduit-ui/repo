<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Repository;
use Illuminate\Support\Collection;

final class RepositoryQuery
{
    protected ?string $user = null;

    protected ?string $org = null;

    protected ?string $visibility = null;

    protected ?string $type = null;

    protected ?string $sort = null;

    protected string $direction = 'desc';

    protected int $perPage = 30;

    protected int $page = 1;

    protected ?string $language = null;

    protected ?int $starsGreaterThan = null;

    protected ?int $forksGreaterThan = null;

    protected ?int $limit = null;

    public function __construct(
        protected Connector $github,
    ) {
    }

    public function user(string $username): self
    {
        $this->user = $username;
        $this->org = null;

        return $this;
    }

    public function org(string $organization): self
    {
        $this->org = $organization;
        $this->user = null;

        return $this;
    }

    public function visibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function sort(string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function direction(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function page(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function whereOwner(string $owner): self
    {
        return $this->user($owner);
    }

    public function whereType(string $type): self
    {
        return $this->type($type);
    }

    public function whereLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function whereStarsGreaterThan(int $count): self
    {
        $this->starsGreaterThan = $count;

        return $this;
    }

    public function whereForksGreaterThan(int $count): self
    {
        $this->forksGreaterThan = $count;

        return $this;
    }

    public function latest(): self
    {
        $this->sort = 'updated';
        $this->direction = 'desc';

        return $this;
    }

    public function oldest(): self
    {
        $this->sort = 'updated';
        $this->direction = 'asc';

        return $this;
    }

    public function limit(int $count): self
    {
        $this->limit = $count;

        return $this;
    }

    public function get(): Collection
    {
        $endpoint = $this->buildEndpoint();
        $params = $this->buildParams();

        $response = $this->github->get($endpoint, $params);

        $collection = collect($response->json())
            ->map(fn (array $repo) => Repository::fromArray($repo));

        return $this->applyClientSideFilters($collection);
    }

    protected function applyClientSideFilters(Collection $collection): Collection
    {
        if ($this->language !== null) {
            $collection = $collection->filter(
                fn (Repository $repo) => $repo->language === $this->language
            );
        }

        if ($this->starsGreaterThan !== null) {
            $collection = $collection->filter(
                fn (Repository $repo) => $repo->stargazersCount > $this->starsGreaterThan
            );
        }

        if ($this->forksGreaterThan !== null) {
            $collection = $collection->filter(
                fn (Repository $repo) => $repo->forksCount > $this->forksGreaterThan
            );
        }

        if ($this->limit !== null) {
            $collection = $collection->take($this->limit);
        }

        return $collection->values();
    }

    protected function buildEndpoint(): string
    {
        if ($this->user !== null) {
            return "/users/{$this->user}/repos";
        }

        if ($this->org !== null) {
            return "/orgs/{$this->org}/repos";
        }

        return '/user/repos';
    }

    protected function buildParams(): array
    {
        $params = [
            'per_page' => $this->perPage,
            'page' => $this->page,
        ];

        if ($this->visibility !== null) {
            $params['visibility'] = $this->visibility;
        }

        if ($this->type !== null) {
            $params['type'] = $this->type;
        }

        if ($this->sort !== null) {
            $params['sort'] = $this->sort;
        }

        $params['direction'] = $this->direction;

        return $params;
    }
}
