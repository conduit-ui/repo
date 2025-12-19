<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Data;

use DateTimeImmutable;

final class WorkflowRun
{
    public function __construct(
        public int $id,
        public string $name,
        public string $status,
        public ?string $conclusion,
        public int $workflow_id,
        public string $head_branch,
        public int $run_number,
        public string $event,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null,
        public ?DateTimeImmutable $runStartedAt = null,
        public ?string $url = null,
        public ?string $htmlUrl = null,
        public ?array $actor = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            status: $data['status'],
            conclusion: $data['conclusion'] ?? null,
            workflow_id: $data['workflow_id'],
            head_branch: $data['head_branch'],
            run_number: $data['run_number'],
            event: $data['event'],
            createdAt: isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null,
            runStartedAt: isset($data['run_started_at']) ? new DateTimeImmutable($data['run_started_at']) : null,
            url: $data['url'] ?? null,
            htmlUrl: $data['html_url'] ?? null,
            actor: $data['actor'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'conclusion' => $this->conclusion,
            'workflow_id' => $this->workflow_id,
            'head_branch' => $this->head_branch,
            'run_number' => $this->run_number,
            'event' => $this->event,
            'created_at' => $this->createdAt?->format('c'),
            'updated_at' => $this->updatedAt?->format('c'),
            'run_started_at' => $this->runStartedAt?->format('c'),
            'url' => $this->url,
            'html_url' => $this->htmlUrl,
            'actor' => $this->actor,
        ];
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    public function isSuccess(): bool
    {
        return $this->conclusion === 'success';
    }

    public function isFailure(): bool
    {
        return $this->conclusion === 'failure';
    }

    public function isCancelled(): bool
    {
        return $this->conclusion === 'cancelled';
    }
}
