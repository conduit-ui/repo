<?php

declare(strict_types=1);

namespace ConduitUI\Repos\Contracts;

interface RepositoryDataContract
{
    public static function fromArray(array $data): self;

    public function toArray(): array;
}
