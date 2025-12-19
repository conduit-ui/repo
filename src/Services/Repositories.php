<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Contracts\RepositoryContract;
use ConduitUI\Repos\Contracts\RepositoryDataContract;
use ConduitUI\Repos\Data\Branch;
use ConduitUI\Repos\Data\Collaborator;
use ConduitUI\Repos\Data\Release;
use ConduitUI\Repos\Data\Repository;
use ConduitUI\Repos\Data\Webhook;
use ConduitUI\Repos\Data\Workflow;
use Illuminate\Support\Collection;

final class Repositories implements RepositoryContract
{
    public function __construct(
        protected Connector $github,
    ) {}

    public function find(string $fullName): RepositoryDataContract
    {
        $response = $this->github->get("/repos/{$fullName}");

        return Repository::fromArray($response->json());
    }

    public function forUser(string $username): RepositoryQuery
    {
        return $this->query()->user($username);
    }

    public function forOrg(string $organization): RepositoryQuery
    {
        return $this->query()->org($organization);
    }

    public function forAuthenticatedUser(): RepositoryQuery
    {
        return $this->query();
    }

    public function create(array $attributes): RepositoryDataContract
    {
        $endpoint = isset($attributes['org'])
            ? "/orgs/{$attributes['org']}/repos"
            : '/user/repos';

        unset($attributes['org']);

        $response = $this->github->post($endpoint, $attributes);

        return Repository::fromArray($response->json());
    }

    public function update(string $fullName, array $attributes): RepositoryDataContract
    {
        $response = $this->github->patch("/repos/{$fullName}", $attributes);

        return Repository::fromArray($response->json());
    }

    public function delete(string $fullName): bool
    {
        $response = $this->github->delete("/repos/{$fullName}");

        return $response->successful();
    }

    public function branches(string $fullName): Collection
    {
        $response = $this->github->get("/repos/{$fullName}/branches");

        return collect($response->json())
            ->map(fn (array $branch) => Branch::fromArray($branch));
    }

    public function releases(string $fullName): Collection
    {
        $response = $this->github->get("/repos/{$fullName}/releases");

        return collect($response->json())
            ->map(fn (array $release) => Release::fromArray($release));
    }

    public function collaborators(string $fullName): Collection
    {
        $response = $this->github->get("/repos/{$fullName}/collaborators");

        return collect($response->json())
            ->map(fn (array $collaborator) => Collaborator::fromArray($collaborator));
    }

    public function topics(string $fullName): array
    {
        $response = $this->github->get("/repos/{$fullName}/topics", [], [
            'Accept' => 'application/vnd.github.mercy-preview+json',
        ]);

        return $response->json()['names'] ?? [];
    }

    public function languages(string $fullName): array
    {
        $response = $this->github->get("/repos/{$fullName}/languages");

        return $response->json();
    }

    public function query(): RepositoryQuery
    {
        return new RepositoryQuery($this->github);
    }

    public function branchQuery(string $fullName): BranchQuery
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        return new BranchQuery($this->github, $owner, $repo);
    }

    public function findBranch(string $fullName, string $branchName): Branch
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        return (new BranchQuery($this->github, $owner, $repo))->find($branchName);
    }

    public function createBranch(string $fullName, string $branchName, string $sha): bool
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        return (new BranchQuery($this->github, $owner, $repo))->create($branchName, $sha);
    }

    public function deleteBranch(string $fullName, string $branchName): bool
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        return (new BranchQuery($this->github, $owner, $repo))->delete($branchName);
    }

    public function webhooks(string $fullName): WebhookQuery
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        return new WebhookQuery($this->github, $owner, $repo);
    }

    public function createWebhook(string $fullName, array $config): Webhook
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        return (new WebhookQuery($this->github, $owner, $repo))->create($config);
    }

    public function deleteWebhook(string $fullName, int $webhookId): bool
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        return (new WebhookQuery($this->github, $owner, $repo))->delete($webhookId);
    }

    public function workflows(string $fullName): WorkflowQuery
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        return new WorkflowQuery($this->github, $owner, $repo);
    }

    public function workflow(string $fullName, string $workflowId): Workflow
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        $query = new WorkflowQuery($this->github, $owner, $repo);

        return is_numeric($workflowId)
            ? $query->find((int) $workflowId)
            : $query->findByFilename($workflowId);
    }

    public function dispatchWorkflow(string $fullName, string $workflowId, array $inputs): bool
    {
        [$owner, $repo] = explode('/', $fullName, 2);

        $query = new WorkflowQuery($this->github, $owner, $repo);

        return is_numeric($workflowId)
            ? $query->dispatch((int) $workflowId, $inputs)
            : $query->dispatchByFilename($workflowId, $inputs);
    }
}
