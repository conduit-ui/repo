<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\FileContent;

final class FileQuery
{
    protected ?string $path = null;

    protected ?string $ref = null;

    protected ?string $branch = null;

    public function __construct(
        protected Connector $github,
        protected string $owner,
        protected string $repo,
    ) {}

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function ref(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function branch(string $branch): self
    {
        $this->branch = $branch;
        $this->ref = $branch;

        return $this;
    }

    public function get(): FileContent
    {
        if ($this->path === null) {
            throw new \InvalidArgumentException('File path is required');
        }

        $params = [];

        if ($this->ref !== null) {
            $params['ref'] = $this->ref;
        }

        $response = $this->github->get(
            "/repos/{$this->owner}/{$this->repo}/contents/{$this->path}",
            $params,
        );

        return FileContent::fromArray($response->json());
    }

    public function create(string $path, string $content, string $message, ?string $branch = null): FileContent
    {
        $payload = [
            'message' => $message,
            'content' => base64_encode($content),
        ];

        if ($branch !== null) {
            $payload['branch'] = $branch;
        }

        $response = $this->github->put(
            "/repos/{$this->owner}/{$this->repo}/contents/{$path}",
            $payload,
        );

        return FileContent::fromArray($response->json()['content']);
    }

    public function update(string $path, string $content, string $message, string $sha, ?string $branch = null): FileContent
    {
        $payload = [
            'message' => $message,
            'content' => base64_encode($content),
            'sha' => $sha,
        ];

        if ($branch !== null) {
            $payload['branch'] = $branch;
        }

        $response = $this->github->put(
            "/repos/{$this->owner}/{$this->repo}/contents/{$path}",
            $payload,
        );

        return FileContent::fromArray($response->json()['content']);
    }

    public function delete(string $path, string $message, string $sha, ?string $branch = null): bool
    {
        $payload = [
            'message' => $message,
            'sha' => $sha,
        ];

        if ($branch !== null) {
            $payload['branch'] = $branch;
        }

        $response = $this->github->delete(
            "/repos/{$this->owner}/{$this->repo}/contents/{$path}",
            $payload,
        );

        return $response->successful();
    }
}
