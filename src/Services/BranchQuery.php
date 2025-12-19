<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Branch;
use Illuminate\Support\Collection;

final class BranchQuery
{
    protected ?bool $protected = null;

    protected ?string $name = null;

    public function __construct(
        protected Connector $github,
        protected string $owner,
        protected string $repo,
    ) {}

    public function whereProtected(): self
    {
        $this->protected = true;

        return $this;
    }

    public function whereNotProtected(): self
    {
        $this->protected = false;

        return $this;
    }

    public function whereName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function get(): Collection
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/branches", []);

        $branches = collect($response->json())
            ->map(fn (array $branch) => Branch::fromArray($branch));

        return $this->applyFilters($branches);
    }

    public function find(string $name): Branch
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/branches/{$name}");

        return Branch::fromArray($response->json());
    }

    public function create(string $name, string $sha): bool
    {
        $response = $this->github->post("/repos/{$this->owner}/{$this->repo}/git/refs", [
            'ref' => "refs/heads/{$name}",
            'sha' => $sha,
        ]);

        return $response->json() !== null;
    }

    public function delete(string $name): bool
    {
        $response = $this->github->delete("/repos/{$this->owner}/{$this->repo}/git/refs/heads/{$name}");

        return $response->successful();
    }

    protected function applyFilters(Collection $branches): Collection
    {
        if ($this->protected !== null) {
            $branches = $branches->filter(fn (Branch $branch) => $branch->protected === $this->protected);
        }

        if ($this->name !== null) {
            $branches = $branches->filter(fn (Branch $branch) => $branch->name === $this->name);
        }

        return $branches->values();
    }
}
