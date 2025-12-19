<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Contracts;

use Illuminate\Support\Collection;

interface RepositoryContract
{
    public function find(string $fullName): RepositoryDataContract;

    public function create(array $attributes): RepositoryDataContract;

    public function update(string $fullName, array $attributes): RepositoryDataContract;

    public function delete(string $fullName): bool;

    public function branches(string $fullName): Collection;

    public function releases(string $fullName): Collection;

    public function collaborators(string $fullName): Collection;

    public function topics(string $fullName): array;

    public function languages(string $fullName): array;
}
