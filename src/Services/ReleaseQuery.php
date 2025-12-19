<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Release;
use ConduitUI\Repos\Data\ReleaseAsset;
use Illuminate\Support\Collection;

final class ReleaseQuery
{
    protected ?bool $excludeDrafts = null;

    protected ?bool $excludePrereleases = null;

    protected ?bool $draftsOnly = null;

    protected ?bool $prereleasesOnly = null;

    protected bool $latestOnly = false;

    public function __construct(
        protected Connector $github,
        protected string $owner,
        protected string $repo,
    ) {}

    public function latest(): self
    {
        $this->latestOnly = true;

        return $this;
    }

    public function excludeDrafts(): self
    {
        $this->excludeDrafts = true;
        $this->draftsOnly = null;

        return $this;
    }

    public function drafts(): self
    {
        $this->draftsOnly = true;
        $this->excludeDrafts = null;

        return $this;
    }

    public function excludePrereleases(): self
    {
        $this->excludePrereleases = true;
        $this->prereleasesOnly = null;

        return $this;
    }

    public function prereleases(): self
    {
        $this->prereleasesOnly = true;
        $this->excludePrereleases = null;

        return $this;
    }

    public function get(): Collection
    {
        if ($this->latestOnly) {
            $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/releases/latest", []);
            $release = Release::fromArray($response->json());

            return collect([$release]);
        }

        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/releases", []);

        $releases = collect($response->json())
            ->map(fn (array $release) => Release::fromArray($release));

        return $this->applyFilters($releases);
    }

    public function first(): ?Release
    {
        return $this->get()->first();
    }

    public function find(string $tag): Release
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/releases/tags/{$tag}");

        return Release::fromArray($response->json());
    }

    public function create(array $attributes): Release
    {
        $response = $this->github->post("/repos/{$this->owner}/{$this->repo}/releases", $attributes);

        return Release::fromArray($response->json());
    }

    public function update(int $releaseId, array $attributes): Release
    {
        $response = $this->github->patch("/repos/{$this->owner}/{$this->repo}/releases/{$releaseId}", $attributes);

        return Release::fromArray($response->json());
    }

    public function delete(int $releaseId): bool
    {
        $response = $this->github->delete("/repos/{$this->owner}/{$this->repo}/releases/{$releaseId}");

        return $response->successful();
    }

    public function uploadAsset(int $releaseId, string $filePath, string $fileName, string $contentType): ReleaseAsset
    {
        $response = $this->github->upload(
            "/repos/{$this->owner}/{$this->repo}/releases/{$releaseId}/assets",
            $filePath,
            $fileName,
            $contentType,
        );

        return ReleaseAsset::fromArray($response->json());
    }

    public function deleteAsset(int $assetId): bool
    {
        $response = $this->github->delete("/repos/{$this->owner}/{$this->repo}/releases/assets/{$assetId}");

        return $response->successful();
    }

    public function generateNotes(string $tagName, ?string $previousTagName = null): array
    {
        $data = ['tag_name' => $tagName];

        if ($previousTagName !== null) {
            $data['previous_tag_name'] = $previousTagName;
        }

        $response = $this->github->post("/repos/{$this->owner}/{$this->repo}/releases/generate-notes", $data);

        return $response->json();
    }

    /**
     * @param  Collection<int, Release>  $releases
     * @return Collection<int, Release>
     */
    protected function applyFilters(Collection $releases): Collection
    {
        if ($this->excludeDrafts === true) {
            $releases = $releases->filter(
                /** @param Release $release */
                fn (Release $release) => ! $release->draft
            );
        }

        if ($this->draftsOnly === true) {
            $releases = $releases->filter(
                /** @param Release $release */
                fn (Release $release) => $release->draft
            );
        }

        if ($this->excludePrereleases === true) {
            $releases = $releases->filter(
                /** @param Release $release */
                fn (Release $release) => ! $release->prerelease
            );
        }

        if ($this->prereleasesOnly === true) {
            $releases = $releases->filter(
                /** @param Release $release */
                fn (Release $release) => $release->prerelease
            );
        }

        return $releases->values();
    }
}
