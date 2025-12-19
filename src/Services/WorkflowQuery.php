<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Workflow;
use Illuminate\Support\Collection;

final class WorkflowQuery
{
    protected ?string $state = null;

    public function __construct(
        protected Connector $github,
        protected string $owner,
        protected string $repo,
    ) {}

    public function whereState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function get(): Collection
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/actions/workflows");

        $workflows = collect($response->json()['workflows'] ?? [])
            ->map(fn (array $workflow) => Workflow::fromArray($workflow));

        return $this->applyFilters($workflows);
    }

    public function find(int $id): Workflow
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/actions/workflows/{$id}");

        return Workflow::fromArray($response->json());
    }

    public function findByFilename(string $filename): Workflow
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/actions/workflows/{$filename}");

        return Workflow::fromArray($response->json());
    }

    public function dispatch(int $id, array $inputs): bool
    {
        $response = $this->github->post(
            "/repos/{$this->owner}/{$this->repo}/actions/workflows/{$id}/dispatches",
            $inputs
        );

        return $response->successful();
    }

    public function dispatchByFilename(string $filename, array $inputs): bool
    {
        $response = $this->github->post(
            "/repos/{$this->owner}/{$this->repo}/actions/workflows/{$filename}/dispatches",
            $inputs
        );

        return $response->successful();
    }

    protected function applyFilters(Collection $workflows): Collection
    {
        if ($this->state !== null) {
            $workflows = $workflows->filter(fn (Workflow $workflow) => $workflow->state === $this->state);
        }

        return $workflows->values();
    }
}
