<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Collaborator;
use Illuminate\Support\Collection;

final class CollaboratorQuery
{
    protected ?string $permissionFilter = null;

    protected ?int $limitCount = null;

    protected ?string $affiliationParam = null;

    protected ?string $permissionParam = null;

    public function __construct(
        protected Connector $github,
        protected string $fullName,
    ) {}

    public function wherePermission(string $permission): self
    {
        $this->permissionFilter = $permission;

        return $this;
    }

    public function affiliation(string $affiliation): self
    {
        $this->affiliationParam = $affiliation;

        return $this;
    }

    public function permission(string $permission): self
    {
        $this->permissionParam = $permission;

        return $this;
    }

    public function limit(int $count): self
    {
        $this->limitCount = $count;

        return $this;
    }

    public function get(): Collection
    {
        $params = $this->buildParams();

        $response = $this->github->get("/repos/{$this->fullName}/collaborators", $params);

        $collection = collect($response->json())
            ->map(fn (array $collaborator) => Collaborator::fromArray($collaborator));

        return $this->applyClientSideFilters($collection);
    }

    public function first(): ?Collaborator
    {
        return $this->limit(1)->get()->first();
    }

    protected function applyClientSideFilters(Collection $collection): Collection
    {
        if ($this->permissionFilter !== null) {
            $collection = $collection->filter(function (Collaborator $collaborator) {
                return match ($this->permissionFilter) {
                    'admin' => $collaborator->permissions->admin,
                    'maintain' => $collaborator->permissions->maintain,
                    'push' => $collaborator->permissions->push,
                    'triage' => $collaborator->permissions->triage,
                    'pull' => $collaborator->permissions->pull,
                    default => false,
                };
            });
        }

        if ($this->limitCount !== null) {
            $collection = $collection->take($this->limitCount);
        }

        return $collection->values();
    }

    protected function buildParams(): array
    {
        $params = [];

        if ($this->affiliationParam !== null) {
            $params['affiliation'] = $this->affiliationParam;
        }

        if ($this->permissionParam !== null) {
            $params['permission'] = $this->permissionParam;
        }

        return $params;
    }
}
