<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Content;
use Illuminate\Support\Collection;

final class ContentQuery
{
    protected ?string $ref = null;

    protected ?string $type = null;

    protected ?string $extension = null;

    public function __construct(
        protected Connector $github,
        protected string $owner,
        protected string $repo,
    ) {}

    public function ref(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function whereType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function whereExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function get(string $path): Content
    {
        $params = $this->buildParams();

        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/contents/{$path}", $params);

        return Content::fromArray($response->json());
    }

    public function list(string $path = ''): Collection
    {
        $params = $this->buildParams();

        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/contents/{$path}", $params);

        $contents = collect($response->json())
            ->map(fn (array $item) => Content::fromArray($item));

        return $this->applyFilters($contents);
    }

    public function readme(): Content
    {
        $params = $this->buildParams();

        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/readme", $params);

        return Content::fromArray($response->json());
    }

    public function create(string $path, array $data): array
    {
        $payload = $this->buildCreateUpdatePayload($data);

        $response = $this->github->put("/repos/{$this->owner}/{$this->repo}/contents/{$path}", $payload);

        return $response->json();
    }

    public function update(string $path, array $data): array
    {
        $payload = $this->buildCreateUpdatePayload($data);

        $response = $this->github->put("/repos/{$this->owner}/{$this->repo}/contents/{$path}", $payload);

        return $response->json();
    }

    public function delete(string $path, array $data): bool
    {
        $payload = [
            'message' => $data['message'],
            'sha' => $data['sha'],
        ];

        if (isset($data['branch'])) {
            $payload['branch'] = $data['branch'];
        }

        $response = $this->github->delete("/repos/{$this->owner}/{$this->repo}/contents/{$path}", $payload);

        return $response->successful();
    }

    protected function buildParams(): array
    {
        $params = [];

        if ($this->ref !== null) {
            $params['ref'] = $this->ref;
        }

        return $params;
    }

    protected function buildCreateUpdatePayload(array $data): array
    {
        $payload = [
            'message' => $data['message'],
        ];

        if (isset($data['encoded']) && $data['encoded'] === true) {
            $payload['content'] = $data['content'];
        } else {
            $payload['content'] = base64_encode($data['content']);
        }

        if (isset($data['sha'])) {
            $payload['sha'] = $data['sha'];
        }

        if (isset($data['branch'])) {
            $payload['branch'] = $data['branch'];
        }

        if (isset($data['committer'])) {
            $payload['committer'] = $data['committer'];
        }

        if (isset($data['author'])) {
            $payload['author'] = $data['author'];
        }

        return $payload;
    }

    protected function applyFilters(Collection $contents): Collection
    {
        if ($this->type !== null) {
            $contents = $contents->filter(fn (Content $content) => $content->type === $this->type);
        }

        if ($this->extension !== null) {
            $contents = $contents->filter(function (Content $content) {
                $extension = pathinfo($content->name, PATHINFO_EXTENSION);

                return $extension === $this->extension;
            });
        }

        return $contents->values();
    }
}
