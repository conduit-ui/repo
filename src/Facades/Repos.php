<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Facades;

use ConduitUI\Repos\Data\Content;
use ConduitUI\Repos\Data\Repository;
use ConduitUI\Repos\Services\ContentQuery;
use ConduitUI\Repos\Services\Repositories;
use ConduitUI\Repos\Services\RepositoryQuery;
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
 * @method static ContentQuery contentQuery(string $fullName)
 * @method static Content contents(string $fullName, string $path)
 * @method static array createFile(string $fullName, string $path, array $data)
 * @method static array updateFile(string $fullName, string $path, array $data)
 * @method static bool deleteFile(string $fullName, string $path, array $data)
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
