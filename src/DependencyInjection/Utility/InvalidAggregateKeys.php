<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\Utility;

use BadMethodCallException;
use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Aggregate\AggregateKey;
use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Aggregate\AggregateKeys;
use Ramsey\Uuid\UuidInterface;

class InvalidAggregateKeys implements AggregateKeys
{
    public function add(AggregateKey $aggregateKey): void
    {
        throw new BadMethodCallException();
    }

    public function withAggregateId(UuidInterface $aggregateId): ?AggregateKey
    {
        throw new BadMethodCallException();
    }

    public function update(AggregateKey $aggregateKey): void
    {
        throw new BadMethodCallException();
    }
}
