<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\FileContent;
use Illuminate\Support\Collection;

final class ContentQuery
{
    protected ?string $filePath = null;

    protected ?string $ref = null;

    public function __construct(
        protected Connector $github,
        protected string $repository,
    ) {}

    public function path(string $path): self
    {
        $this->filePath = $path;

        return $this;
    }

    public function ref(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function get(): FileContent
    {
        $endpoint = "/repos/{$this->repository}/contents/{$this->filePath}";
        $params = $this->buildParams();

        $response = $this->github->get($endpoint, $params);

        return FileContent::fromArray($response->json());
    }

    public function content(): string
    {
        $file = $this->get();

        return $file->decoded() ?? '';
    }

    public function raw(): string
    {
        return $this->content();
    }

    public function download(): string
    {
        return $this->content();
    }

    public function json(): array
    {
        $file = $this->get();

        return $file->json() ?? [];
    }

    public function list(): Collection
    {
        $endpoint = "/repos/{$this->repository}/contents/{$this->filePath}";
        $params = $this->buildParams();

        $response = $this->github->get($endpoint, $params);
        $data = $response->json();

        return collect($data)
            ->map(fn (array $item) => FileContent::fromArray($item));
    }

    public function create(string $content, string $message, ?string $branch = null): FileContent
    {
        $endpoint = "/repos/{$this->repository}/contents/{$this->filePath}";
        $params = [
            'message' => $message,
            'content' => base64_encode($content),
        ];

        if ($branch !== null) {
            $params['branch'] = $branch;
        }

        $response = $this->github->put($endpoint, $params);

        return FileContent::fromArray($response->json()['content']);
    }

    public function update(string $content, string $sha, string $message, ?string $branch = null): FileContent
    {
        $endpoint = "/repos/{$this->repository}/contents/{$this->filePath}";
        $params = [
            'message' => $message,
            'content' => base64_encode($content),
            'sha' => $sha,
        ];

        if ($branch !== null) {
            $params['branch'] = $branch;
        }

        $response = $this->github->put($endpoint, $params);

        return FileContent::fromArray($response->json()['content']);
    }

    public function delete(string $sha, string $message, ?string $branch = null): bool
    {
        $endpoint = "/repos/{$this->repository}/contents/{$this->filePath}";
        $params = [
            'message' => $message,
            'sha' => $sha,
        ];

        if ($branch !== null) {
            $params['branch'] = $branch;
        }

        $response = $this->github->delete($endpoint, $params);

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
}
