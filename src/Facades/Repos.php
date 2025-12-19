<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Facades;

use ConduitUI\Repos\Data\Repository;
use ConduitUI\Repos\Data\Webhook;
use ConduitUI\Repos\Data\Workflow;
use ConduitUI\Repos\Services\Repositories;
use ConduitUI\Repos\Services\RepositoryQuery;
use ConduitUI\Repos\Services\WebhookQuery;
use ConduitUI\Repos\Services\WorkflowQuery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Repository find(string $fullName)
 * @method static RepositoryQuery forUser(string $username)
 * @method static RepositoryQuery forOrg(string $organization)
 * @method static RepositoryQuery forAuthenticatedUser()
 * @method static Repository create(array $attributes)
 * @method static Repository update(string $fullName, array $attributes)
 * @method static bool delete(string $fullName)
 * @method static Collection branches(string $fullName)
 * @method static Collection releases(string $fullName)
 * @method static Collection collaborators(string $fullName)
 * @method static array topics(string $fullName)
 * @method static array languages(string $fullName)
 * @method static RepositoryQuery query()
 * @method static WebhookQuery webhooks(string $fullName)
 * @method static Webhook createWebhook(string $fullName, array $config)
 * @method static bool deleteWebhook(string $fullName, int $webhookId)
 * @method static WorkflowQuery workflows(string $fullName)
 * @method static Workflow workflow(string $fullName, string $workflowId)
 * @method static bool dispatchWorkflow(string $fullName, string $workflowId, array $inputs)
 *
 * @see Repositories
 */
final class Repos extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Repositories::class;
    }
}
