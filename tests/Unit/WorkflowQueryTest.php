<?php

declare(strict_types=1);

use ConduitUi\GitHubConnector\Connector;
use ConduitUI\Repos\Data\Workflow;
use ConduitUI\Repos\Services\WorkflowQuery;
use Illuminate\Http\Client\Response;
use Mockery as m;

beforeEach(function () {
    $this->connector = m::mock(Connector::class);
    $this->query = new WorkflowQuery($this->connector, 'owner', 'repo');
});

afterEach(function () {
    m::close();
});

it('can get all workflows', function () {
    $responseData = [
        'total_count' => 2,
        'workflows' => [
            [
                'id' => 1,
                'name' => 'CI',
                'path' => '.github/workflows/ci.yml',
                'state' => 'active',
                'created_at' => '2024-01-01T00:00:00Z',
                'updated_at' => '2024-01-02T00:00:00Z',
            ],
            [
                'id' => 2,
                'name' => 'Deploy',
                'path' => '.github/workflows/deploy.yml',
                'state' => 'active',
                'created_at' => '2024-01-01T00:00:00Z',
                'updated_at' => '2024-01-02T00:00:00Z',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/workflows')
        ->andReturn($response);

    $workflows = $this->query->get();

    expect($workflows)->toHaveCount(2);
    expect($workflows->first())->toBeInstanceOf(Workflow::class);
    expect($workflows->first()->name)->toBe('CI');
    expect($workflows->last()->name)->toBe('Deploy');
});

it('can filter workflows by state', function () {
    $responseData = [
        'total_count' => 2,
        'workflows' => [
            [
                'id' => 1,
                'name' => 'CI',
                'path' => '.github/workflows/ci.yml',
                'state' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Deploy',
                'path' => '.github/workflows/deploy.yml',
                'state' => 'disabled',
            ],
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/workflows')
        ->andReturn($response);

    $workflows = $this->query->whereState('active')->get();

    expect($workflows)->toHaveCount(1);
    expect($workflows->first()->state)->toBe('active');
});

it('can find a specific workflow by id', function () {
    $responseData = [
        'id' => 1,
        'name' => 'CI',
        'path' => '.github/workflows/ci.yml',
        'state' => 'active',
        'created_at' => '2024-01-01T00:00:00Z',
        'updated_at' => '2024-01-02T00:00:00Z',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/workflows/1')
        ->andReturn($response);

    $workflow = $this->query->find(1);

    expect($workflow)->toBeInstanceOf(Workflow::class);
    expect($workflow->id)->toBe(1);
    expect($workflow->name)->toBe('CI');
});

it('can find a workflow by filename', function () {
    $responseData = [
        'id' => 1,
        'name' => 'CI',
        'path' => '.github/workflows/ci.yml',
        'state' => 'active',
        'created_at' => '2024-01-01T00:00:00Z',
        'updated_at' => '2024-01-02T00:00:00Z',
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('get')
        ->with('/repos/owner/repo/actions/workflows/ci.yml')
        ->andReturn($response);

    $workflow = $this->query->findByFilename('ci.yml');

    expect($workflow)->toBeInstanceOf(Workflow::class);
    expect($workflow->name)->toBe('CI');
    expect($workflow->path)->toBe('.github/workflows/ci.yml');
});

it('can dispatch a workflow by id', function () {
    $inputs = [
        'ref' => 'main',
        'inputs' => [
            'environment' => 'production',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/actions/workflows/1/dispatches', $inputs)
        ->andReturn($response);

    $result = $this->query->dispatch(1, $inputs);

    expect($result)->toBeTrue();
});

it('can dispatch a workflow by filename', function () {
    $inputs = [
        'ref' => 'main',
        'inputs' => [
            'environment' => 'production',
        ],
    ];

    $response = m::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);

    $this->connector
        ->shouldReceive('post')
        ->with('/repos/owner/repo/actions/workflows/deploy.yml/dispatches', $inputs)
        ->andReturn($response);

    $result = $this->query->dispatchByFilename('deploy.yml', $inputs);

    expect($result)->toBeTrue();
});
