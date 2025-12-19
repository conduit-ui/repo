<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Services;

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Webhook;
use Illuminate\Support\Collection;

final class WebhookQuery
{
    protected int $perPage = 30;

    protected int $page = 1;

    protected ?bool $activeFilter = null;

    protected ?string $eventFilter = null;

    public function __construct(
        protected Connector $github,
        protected string $repository,
    ) {}

    public function get(): Collection
    {
        $response = $this->github->get(
            "/repos/{$this->repository}/hooks",
            [
                'per_page' => $this->perPage,
                'page' => $this->page,
            ],
        );

        $collection = collect($response->json())
            ->map(fn (array $webhook) => Webhook::fromArray($webhook));

        return $this->applyFilters($collection);
    }

    public function find(int $id): Webhook
    {
        $response = $this->github->get("/repos/{$this->repository}/hooks/{$id}");

        return Webhook::fromArray($response->json());
    }

    public function create(
        string $url,
        array $events = ['push'],
        ?string $contentType = 'json',
        bool $active = true,
        ?string $secret = null,
        bool $insecureSsl = false,
    ): Webhook {
        $config = [
            'url' => $url,
            'content_type' => $contentType,
        ];

        if ($secret !== null) {
            $config['secret'] = $secret;
        }

        if ($insecureSsl) {
            $config['insecure_ssl'] = '1';
        }

        $data = [
            'name' => 'web',
            'active' => $active,
            'events' => $events,
            'config' => $config,
        ];

        $response = $this->github->post("/repos/{$this->repository}/hooks", $data);

        return Webhook::fromArray($response->json());
    }

    public function update(int $id, array $attributes): Webhook
    {
        $response = $this->github->patch(
            "/repos/{$this->repository}/hooks/{$id}",
            $attributes,
        );

        return Webhook::fromArray($response->json());
    }

    public function delete(int $id): bool
    {
        $response = $this->github->delete("/repos/{$this->repository}/hooks/{$id}");

        return $response->successful();
    }

    public function ping(int $id): bool
    {
        $response = $this->github->post("/repos/{$this->repository}/hooks/{$id}/pings", []);

        return $response->successful();
    }

    public function test(int $id): bool
    {
        $response = $this->github->post("/repos/{$this->repository}/hooks/{$id}/tests", []);

        return $response->successful();
    }

    public function whereActive(bool $active): self
    {
        $this->activeFilter = $active;

        return $this;
    }

    public function whereEvent(string $event): self
    {
        $this->eventFilter = $event;

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

    protected function applyFilters(Collection $collection): Collection
    {
        if ($this->activeFilter !== null) {
            $collection = $collection->filter(
                fn (Webhook $webhook) => $webhook->active === $this->activeFilter,
            );
        }

        if ($this->eventFilter !== null) {
            $collection = $collection->filter(
                fn (Webhook $webhook) => in_array($this->eventFilter, $webhook->events, true),
            );
        }

        return $collection->values();
    }
}
