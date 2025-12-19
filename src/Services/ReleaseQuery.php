<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Release;
use Illuminate\Support\Collection;

final class ReleaseQuery
{
    protected ?bool $draft = null;

    protected ?bool $prerelease = null;

    public function __construct(
        protected Connector $github,
        protected string $owner,
        protected string $repo,
    ) {}

    public function whereDraft(bool $draft): self
    {
        $this->draft = $draft;

        return $this;
    }

    public function wherePrerelease(bool $prerelease): self
    {
        $this->prerelease = $prerelease;

        return $this;
    }

    public function get(): Collection
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/releases", []);

        $releases = collect($response->json())
            ->map(fn (array $release) => Release::fromArray($release));

        return $this->applyFilters($releases);
    }

    public function first(): ?Release
    {
        return $this->get()->first();
    }

    public function latest(): Release
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/releases/latest");

        return Release::fromArray($response->json());
    }

    public function findByTag(string $tag): Release
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/releases/tags/{$tag}");

        return Release::fromArray($response->json());
    }

    public function find(int $id): Release
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/releases/{$id}");

        return Release::fromArray($response->json());
    }

    public function create(array $attributes): Release
    {
        $response = $this->github->post("/repos/{$this->owner}/{$this->repo}/releases", $attributes);

        return Release::fromArray($response->json());
    }

    public function update(int $id, array $attributes): Release
    {
        $response = $this->github->patch("/repos/{$this->owner}/{$this->repo}/releases/{$id}", $attributes);

        return Release::fromArray($response->json());
    }

    public function delete(int $id): bool
    {
        $response = $this->github->delete("/repos/{$this->owner}/{$this->repo}/releases/{$id}");

        return $response->successful();
    }

    protected function applyFilters(Collection $releases): Collection
    {
        if ($this->draft !== null) {
            $releases = $releases->filter(fn (Release $release) => $release->draft === $this->draft);
        }

        if ($this->prerelease !== null) {
            $releases = $releases->filter(fn (Release $release) => $release->prerelease === $this->prerelease);
        }

        return $releases->values();
    }
}
