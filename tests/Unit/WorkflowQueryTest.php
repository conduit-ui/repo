<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Services\WorkflowQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new WorkflowQuery($this->connector, 'owner/repo');
});

afterEach(function () {
    m::close();
});

it('can list workflows for a repository', function () {
    $responseData = [
        'total_count' => 1,
        'workflows' => [
            [
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
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/workflows', [
            'per_page' => 30,
            'page' => 1,
        ])
        ->andReturn($response);

    $workflows = $this->query->get();

    expect($workflows)->toHaveCount(1);
    expect($workflows->first()->id)->toBe(1);
    expect($workflows->first()->name)->toBe('CI');
});

it('can get a specific workflow by id', function () {
    $responseData = [
        'id' => 2,
        'node_id' => 'W_def456',
        'name' => 'Deploy',
        'path' => '.github/workflows/deploy.yml',
        'state' => 'active',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-02T00:00:00Z',
        'url' => 'https://api.github.com/repos/owner/repo/actions/workflows/2',
        'html_url' => 'https://github.com/owner/repo/actions/workflows/deploy.yml',
        'badge_url' => 'https://github.com/owner/repo/workflows/Deploy/badge.svg',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/workflows/2')
        ->andReturn($response);

    $workflow = $this->query->find(2);

    expect($workflow->id)->toBe(2);
    expect($workflow->name)->toBe('Deploy');
});

it('can dispatch a workflow', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/actions/workflows/3/dispatches', [
            'ref' => 'main',
            'inputs' => [
                'environment' => 'production',
            ],
        ])
        ->andReturn($response);

    $result = $this->query->dispatch(3, 'main', ['environment' => 'production']);

    expect($result)->toBeTrue();
});

it('can list workflow runs', function () {
    $responseData = [
        'total_count' => 2,
        'workflow_runs' => [
            [
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
            ],
            [
                'id' => 101,
                'name' => 'CI',
                'status' => 'in_progress',
                'conclusion' => null,
                'workflow_id' => 1,
                'head_branch' => 'develop',
                'run_number' => 43,
                'event' => 'pull_request',
                'created_at' => '2023-01-02T00:00:00Z',
                'updated_at' => '2023-01-02T00:05:00Z',
                'run_started_at' => '2023-01-02T00:01:00Z',
                'url' => 'https://api.github.com/repos/owner/repo/actions/runs/101',
                'html_url' => 'https://github.com/owner/repo/actions/runs/101',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/runs', [
            'per_page' => 30,
            'page' => 1,
        ])
        ->andReturn($response);

    $runs = $this->query->runs()->get();

    expect($runs)->toHaveCount(2);
    expect($runs->first()->id)->toBe(100);
    expect($runs->first()->status)->toBe('completed');
});

it('can get workflow runs for a specific workflow', function () {
    $responseData = [
        'total_count' => 1,
        'workflow_runs' => [
            [
                'id' => 102,
                'name' => 'Deploy',
                'status' => 'completed',
                'conclusion' => 'success',
                'workflow_id' => 2,
                'head_branch' => 'main',
                'run_number' => 10,
                'event' => 'workflow_dispatch',
                'created_at' => '2023-01-03T00:00:00Z',
                'updated_at' => '2023-01-03T00:10:00Z',
                'run_started_at' => '2023-01-03T00:01:00Z',
                'url' => 'https://api.github.com/repos/owner/repo/actions/runs/102',
                'html_url' => 'https://github.com/owner/repo/actions/runs/102',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/workflows/2/runs', [
            'per_page' => 30,
            'page' => 1,
        ])
        ->andReturn($response);

    $runs = $this->query->runs(2)->get();

    expect($runs)->toHaveCount(1);
    expect($runs->first()->workflow_id)->toBe(2);
});

it('can filter workflow runs by status', function () {
    $responseData = [
        'total_count' => 1,
        'workflow_runs' => [
            [
                'id' => 103,
                'name' => 'CI',
                'status' => 'completed',
                'conclusion' => 'success',
                'workflow_id' => 1,
                'head_branch' => 'main',
                'run_number' => 44,
                'event' => 'push',
                'created_at' => '2023-01-04T00:00:00Z',
                'updated_at' => '2023-01-04T00:05:00Z',
                'run_started_at' => '2023-01-04T00:01:00Z',
                'url' => 'https://api.github.com/repos/owner/repo/actions/runs/103',
                'html_url' => 'https://github.com/owner/repo/actions/runs/103',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/runs', m::subset([
            'status' => 'completed',
        ]))
        ->andReturn($response);

    $runs = $this->query->runs()->whereStatus('completed')->get();

    expect($runs)->toHaveCount(1);
    expect($runs->first()->status)->toBe('completed');
});

it('can filter workflow runs by branch', function () {
    $responseData = [
        'total_count' => 1,
        'workflow_runs' => [
            [
                'id' => 104,
                'name' => 'CI',
                'status' => 'completed',
                'conclusion' => 'success',
                'workflow_id' => 1,
                'head_branch' => 'develop',
                'run_number' => 45,
                'event' => 'push',
                'created_at' => '2023-01-05T00:00:00Z',
                'updated_at' => '2023-01-05T00:05:00Z',
                'run_started_at' => '2023-01-05T00:01:00Z',
                'url' => 'https://api.github.com/repos/owner/repo/actions/runs/104',
                'html_url' => 'https://github.com/owner/repo/actions/runs/104',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/runs', m::subset([
            'branch' => 'develop',
        ]))
        ->andReturn($response);

    $runs = $this->query->runs()->whereBranch('develop')->get();

    expect($runs)->toHaveCount(1);
    expect($runs->first()->head_branch)->toBe('develop');
});

it('can filter workflow runs by actor', function () {
    $responseData = [
        'total_count' => 1,
        'workflow_runs' => [
            [
                'id' => 105,
                'name' => 'CI',
                'status' => 'completed',
                'conclusion' => 'success',
                'workflow_id' => 1,
                'head_branch' => 'main',
                'run_number' => 46,
                'event' => 'push',
                'actor' => [
                    'login' => 'octocat',
                ],
                'created_at' => '2023-01-06T00:00:00Z',
                'updated_at' => '2023-01-06T00:05:00Z',
                'run_started_at' => '2023-01-06T00:01:00Z',
                'url' => 'https://api.github.com/repos/owner/repo/actions/runs/105',
                'html_url' => 'https://github.com/owner/repo/actions/runs/105',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/runs', m::subset([
            'actor' => 'octocat',
        ]))
        ->andReturn($response);

    $runs = $this->query->runs()->whereActor('octocat')->get();

    expect($runs)->toHaveCount(1);
});

it('can cancel a workflow run', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/actions/runs/106/cancel', [])
        ->andReturn($response);

    $result = $this->query->cancel(106);

    expect($result)->toBeTrue();
});

it('can rerun a workflow', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/actions/runs/107/rerun', [])
        ->andReturn($response);

    $result = $this->query->rerun(107);

    expect($result)->toBeTrue();
});

it('can get a specific workflow run', function () {
    $responseData = [
        'id' => 108,
        'name' => 'CI',
        'status' => 'completed',
        'conclusion' => 'success',
        'workflow_id' => 1,
        'head_branch' => 'main',
        'run_number' => 47,
        'event' => 'push',
        'created_at' => '2023-01-07T00:00:00Z',
        'updated_at' => '2023-01-07T00:05:00Z',
        'run_started_at' => '2023-01-07T00:01:00Z',
        'url' => 'https://api.github.com/repos/owner/repo/actions/runs/108',
        'html_url' => 'https://github.com/owner/repo/actions/runs/108',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/runs/108')
        ->andReturn($response);

    $run = $this->query->findRun(108);

    expect($run->id)->toBe(108);
    expect($run->conclusion)->toBe('success');
});

it('can set pagination for workflows', function () {
    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn(['total_count' => 0, 'workflows' => []]);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/workflows', [
            'per_page' => 50,
            'page' => 2,
        ])
        ->andReturn($response);

    $result = $this->query->perPage(50)->page(2);

    expect($result)->toBeInstanceOf(WorkflowQuery::class);
    $result->get();
});

it('can filter workflows by state', function () {
    $responseData = [
        'total_count' => 1,
        'workflows' => [
            [
                'id' => 3,
                'node_id' => 'W_ghi789',
                'name' => 'Active Workflow',
                'path' => '.github/workflows/active.yml',
                'state' => 'active',
                'created_at' => '2023-01-01T00:00:00Z',
                'updated_at' => '2023-01-02T00:00:00Z',
                'url' => 'https://api.github.com/repos/owner/repo/actions/workflows/3',
                'html_url' => 'https://github.com/owner/repo/actions/workflows/active.yml',
                'badge_url' => 'https://github.com/owner/repo/workflows/Active/badge.svg',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->andReturn($response);

    $workflows = $this->query->whereState('active')->get();

    expect($workflows)->toHaveCount(1);
    expect($workflows->first()->state)->toBe('active');
});

it('can filter workflow runs by event type', function () {
    $responseData = [
        'total_count' => 1,
        'workflow_runs' => [
            [
                'id' => 109,
                'name' => 'CI',
                'status' => 'completed',
                'conclusion' => 'success',
                'workflow_id' => 1,
                'head_branch' => 'main',
                'run_number' => 48,
                'event' => 'pull_request',
                'created_at' => '2023-01-08T00:00:00Z',
                'updated_at' => '2023-01-08T00:05:00Z',
                'run_started_at' => '2023-01-08T00:01:00Z',
                'url' => 'https://api.github.com/repos/owner/repo/actions/runs/109',
                'html_url' => 'https://github.com/owner/repo/actions/runs/109',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/runs', m::subset([
            'event' => 'pull_request',
        ]))
        ->andReturn($response);

    $runs = $this->query->runs()->whereEvent('pull_request')->get();

    expect($runs)->toHaveCount(1);
    expect($runs->first()->event)->toBe('pull_request');
});
