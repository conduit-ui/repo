<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\WorkflowRun;

it('can create workflow run from array', function () {
    $data = [
        'id' => 100,
        'name' => 'CI',
        'status' => 'completed',
        'conclusion' => 'success',
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 42,
        'event' => 'push',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
        'run_started_at' => '2023-01-01T00:01:00Z',
        'url' => 'https://api.github.com/repos/owner/repo/actions/runs/100',
        'html_url' => 'https://github.com/owner/repo/actions/runs/100',
        'actor' => [
            'login' => 'octocat',
        ],
    ];

    $run = WorkflowRun::fromArray($data);

    expect($run->id)->toBe(100);
    expect($run->name)->toBe('CI');
    expect($run->status)->toBe('completed');
    expect($run->conclusion)->toBe('success');
    expect($run->workflow_id)->toBe(1);
    expect($run->head_branch)->toBe('main');
});

it('can convert workflow run to array', function () {
    $data = [
        'id' => 101,
        'name' => 'Deploy',
        'status' => 'in_progress',
        'conclusion' => null,
        'workflow_id' => 2,
        'head_branch' => 'develop',
        'run_number' => 10,
        'event' => 'workflow_dispatch',
        'created_at' => '2023-01-03T00:00:00Z',
        'updated_at' => '2023-01-03T00:05:00Z',
        'run_started_at' => '2023-01-03T00:01:00Z',
    ];

    $run = WorkflowRun::fromArray($data);
    $array = $run->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe(101);
    expect($array['status'])->toBe('in_progress');
    expect($array['conclusion'])->toBeNull();
});

it('can check if workflow run is completed', function () {
    $completed = WorkflowRun::fromArray([
        'id' => 102,
        'name' => 'Test',
        'status' => 'completed',
        'conclusion' => 'success',
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 1,
        'event' => 'push',
    ]);

    $inProgress = WorkflowRun::fromArray([
        'id' => 103,
        'name' => 'Test',
        'status' => 'in_progress',
        'conclusion' => null,
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 2,
        'event' => 'push',
    ]);

    expect($completed->isCompleted())->toBeTrue();
    expect($inProgress->isCompleted())->toBeFalse();
});

it('can check if workflow run is in progress', function () {
    $inProgress = WorkflowRun::fromArray([
        'id' => 104,
        'name' => 'Test',
        'status' => 'in_progress',
        'conclusion' => null,
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 3,
        'event' => 'push',
    ]);

    expect($inProgress->isInProgress())->toBeTrue();
});

it('can check if workflow run is queued', function () {
    $queued = WorkflowRun::fromArray([
        'id' => 105,
        'name' => 'Test',
        'status' => 'queued',
        'conclusion' => null,
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 4,
        'event' => 'push',
    ]);

    expect($queued->isQueued())->toBeTrue();
});

it('can check if workflow run is success', function () {
    $success = WorkflowRun::fromArray([
        'id' => 106,
        'name' => 'Test',
        'status' => 'completed',
        'conclusion' => 'success',
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 5,
        'event' => 'push',
    ]);

    $failure = WorkflowRun::fromArray([
        'id' => 107,
        'name' => 'Test',
        'status' => 'completed',
        'conclusion' => 'failure',
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 6,
        'event' => 'push',
    ]);

    expect($success->isSuccess())->toBeTrue();
    expect($failure->isSuccess())->toBeFalse();
});

it('can check if workflow run is failure', function () {
    $failure = WorkflowRun::fromArray([
        'id' => 108,
        'name' => 'Test',
        'status' => 'completed',
        'conclusion' => 'failure',
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 7,
        'event' => 'push',
    ]);

    expect($failure->isFailure())->toBeTrue();
});

it('can check if workflow run is cancelled', function () {
    $cancelled = WorkflowRun::fromArray([
        'id' => 109,
        'name' => 'Test',
        'status' => 'completed',
        'conclusion' => 'cancelled',
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 8,
        'event' => 'push',
    ]);

    expect($cancelled->isCancelled())->toBeTrue();
});
