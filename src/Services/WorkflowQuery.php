<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Workflow;
use ConduitUI\Repos\Data\WorkflowRun;
use Illuminate\Support\Collection;

final class WorkflowQuery
{
    protected int $perPage = 30;

    protected int $page = 1;

    protected ?string $stateFilter = null;

    protected ?int $workflowId = null;

    protected ?string $statusFilter = null;

    protected ?string $branchFilter = null;

    protected ?string $actorFilter = null;

    protected ?string $eventFilter = null;

    protected bool $isRunsQuery = false;

    public function __construct(
        protected Connector $github,
        protected string $repository,
    ) {}

    public function get(): Collection
    {
        if ($this->isRunsQuery) {
            return $this->getWorkflowRuns();
        }

        $endpoint = "/repos/{$this->repository}/actions/workflows";

        $response = $this->github->get($endpoint, [
            'per_page' => $this->perPage,
            'page' => $this->page,
        ]);

        $data = $response->json();
        $collection = collect($data['workflows'] ?? [])
            ->map(fn (array $workflow) => Workflow::fromArray($workflow));

        return $this->applyWorkflowFilters($collection);
    }

    public function find(int $id): Workflow
    {
        $response = $this->github->get("/repos/{$this->repository}/actions/workflows/{$id}");

        return Workflow::fromArray($response->json());
    }

    public function dispatch(int $workflowId, string $ref, array $inputs = []): bool
    {
        $response = $this->github->post(
            "/repos/{$this->repository}/actions/workflows/{$workflowId}/dispatches",
            [
                'ref' => $ref,
                'inputs' => $inputs,
            ],
        );

        return $response->successful();
    }

    public function runs(?int $workflowId = null): self
    {
        $this->isRunsQuery = true;
        $this->workflowId = $workflowId;

        return $this;
    }

    public function findRun(int $runId): WorkflowRun
    {
        $response = $this->github->get("/repos/{$this->repository}/actions/runs/{$runId}");

        return WorkflowRun::fromArray($response->json());
    }

    public function cancel(int $runId): bool
    {
        $response = $this->github->post(
            "/repos/{$this->repository}/actions/runs/{$runId}/cancel",
            [],
        );

        return $response->successful();
    }

    public function rerun(int $runId): bool
    {
        $response = $this->github->post(
            "/repos/{$this->repository}/actions/runs/{$runId}/rerun",
            [],
        );

        return $response->successful();
    }

    public function whereStatus(string $status): self
    {
        $this->statusFilter = $status;

        return $this;
    }

    public function whereBranch(string $branch): self
    {
        $this->branchFilter = $branch;

        return $this;
    }

    public function whereActor(string $actor): self
    {
        $this->actorFilter = $actor;

        return $this;
    }

    public function whereEvent(string $event): self
    {
        $this->eventFilter = $event;

        return $this;
    }

    public function whereState(string $state): self
    {
        $this->stateFilter = $state;

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

    protected function applyWorkflowFilters(Collection $collection): Collection
    {
        if ($this->stateFilter !== null) {
            $collection = $collection->filter(
                fn (Workflow $workflow) => $workflow->state === $this->stateFilter,
            );
        }

        return $collection->values();
    }

    protected function buildRunsParams(): array
    {
        $params = [
            'per_page' => $this->perPage,
            'page' => $this->page,
        ];

        if ($this->statusFilter !== null) {
            $params['status'] = $this->statusFilter;
        }

        if ($this->branchFilter !== null) {
            $params['branch'] = $this->branchFilter;
        }

        if ($this->actorFilter !== null) {
            $params['actor'] = $this->actorFilter;
        }

        if ($this->eventFilter !== null) {
            $params['event'] = $this->eventFilter;
        }

        return $params;
    }

    protected function getRunsEndpoint(): string
    {
        if ($this->workflowId !== null) {
            return "/repos/{$this->repository}/actions/workflows/{$this->workflowId}/runs";
        }

        return "/repos/{$this->repository}/actions/runs";
    }

    protected function getWorkflowRuns(): Collection
    {
        $endpoint = $this->getRunsEndpoint();
        $params = $this->buildRunsParams();

        $response = $this->github->get($endpoint, $params);

        $data = $response->json();

        return collect($data['workflow_runs'] ?? [])
            ->map(fn (array $run) => WorkflowRun::fromArray($run))
            ->values();
    }
}
