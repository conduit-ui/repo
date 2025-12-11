<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUI\Connector\GitHub;
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

    public function __construct(
        protected GitHub $github,
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

    public function get(): Collection
    {
        $endpoint = $this->buildEndpoint();
        $params = $this->buildParams();

        $response = $this->github->get($endpoint, $params);

        return collect($response->json())
            ->map(fn (array $repo) => Repository::fromArray($repo));
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
