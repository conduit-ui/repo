<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Webhook;
use Illuminate\Support\Collection;

final class WebhookQuery
{
    protected ?bool $active = null;

    public function __construct(
        protected Connector $github,
        protected string $owner,
        protected string $repo,
    ) {}

    public function whereActive(): self
    {
        $this->active = true;

        return $this;
    }

    public function whereInactive(): self
    {
        $this->active = false;

        return $this;
    }

    public function get(): Collection
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/hooks");

        $webhooks = collect($response->json())
            ->map(fn (array $webhook) => Webhook::fromArray($webhook));

        return $this->applyFilters($webhooks);
    }

    public function find(int $id): Webhook
    {
        $response = $this->github->get("/repos/{$this->owner}/{$this->repo}/hooks/{$id}");

        return Webhook::fromArray($response->json());
    }

    public function create(array $config): Webhook
    {
        $response = $this->github->post("/repos/{$this->owner}/{$this->repo}/hooks", $config);

        return Webhook::fromArray($response->json());
    }

    public function update(int $id, array $config): Webhook
    {
        $response = $this->github->patch("/repos/{$this->owner}/{$this->repo}/hooks/{$id}", $config);

        return Webhook::fromArray($response->json());
    }

    public function delete(int $id): bool
    {
        $response = $this->github->delete("/repos/{$this->owner}/{$this->repo}/hooks/{$id}");

        return $response->successful();
    }

    public function ping(int $id): bool
    {
        $response = $this->github->post("/repos/{$this->owner}/{$this->repo}/hooks/{$id}/pings", []);

        return $response->successful();
    }

    protected function applyFilters(Collection $webhooks): Collection
    {
        if ($this->active !== null) {
            $webhooks = $webhooks->filter(fn (Webhook $webhook) => $webhook->active === $this->active);
        }

        return $webhooks->values();
    }
}
