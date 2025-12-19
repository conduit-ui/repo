<?php

declare(strict_types=1);

use ConduitUI\Repos\Data\Workflow;

it('can create workflow from array', function () {
    $data = [
        'id' => 1,
        'node_id' => 'W_abc123',
        'name' => 'CI',
        'path' => '.github/workflows/ci.yml',
        'state' => 'active',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
        'url' => 'https://api.github.com/repos/owner/repo/actions/workflows/1',
        'html_url' => 'https://github.com/owner/repo/actions/workflows/ci.yml',
        'badge_url' => 'https://github.com/owner/repo/workflows/CI/badge.svg',
    ];

    $workflow = Workflow::fromArray($data);

    expect($workflow->id)->toBe(1);
    expect($workflow->nodeId)->toBe('W_abc123');
    expect($workflow->name)->toBe('CI');
    expect($workflow->path)->toBe('.github/workflows/ci.yml');
    expect($workflow->state)->toBe('active');
});

it('can convert workflow to array', function () {
    $data = [
        'id' => 2,
        'node_id' => 'W_def456',
        'name' => 'Deploy',
        'path' => '.github/workflows/deploy.yml',
        'state' => 'disabled',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
    ];

    $workflow = Workflow::fromArray($data);
    $array = $workflow->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe(2);
    expect($array['name'])->toBe('Deploy');
    expect($array['state'])->toBe('disabled');
});

it('can check if workflow is active', function () {
    $activeWorkflow = Workflow::fromArray([
        'id' => 3,
        'node_id' => 'W_ghi789',
        'name' => 'Test',
        'path' => '.github/workflows/test.yml',
        'state' => 'active',
    ]);

    $disabledWorkflow = Workflow::fromArray([
        'id' => 4,
        'node_id' => 'W_jkl012',
        'name' => 'Disabled',
        'path' => '.github/workflows/disabled.yml',
        'state' => 'disabled',
    ]);

    expect($activeWorkflow->isActive())->toBeTrue();
    expect($disabledWorkflow->isActive())->toBeFalse();
});

it('can check if workflow is disabled', function () {
    $disabledWorkflow = Workflow::fromArray([
        'id' => 5,
        'node_id' => 'W_mno345',
        'name' => 'Old',
        'path' => '.github/workflows/old.yml',
        'state' => 'disabled',
    ]);

    $activeWorkflow = Workflow::fromArray([
        'id' => 6,
        'node_id' => 'W_pqr678',
        'name' => 'New',
        'path' => '.github/workflows/new.yml',
        'state' => 'active',
    ]);

    expect($disabledWorkflow->isDisabled())->toBeTrue();
    expect($activeWorkflow->isDisabled())->toBeFalse();
});
