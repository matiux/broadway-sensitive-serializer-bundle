<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Broadway\Bundle\BroadwayBundle\DependencyInjection\CompilerPass;
use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Aggregate\AggregateKeys;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webmozart\Assert\Assert;

class RegisterAggregateKeysCompilerPass extends CompilerPass
{
    public function process(ContainerBuilder $container): void
    {
        $serviceParameter = 'broadway_sensitive_serializer.aggregate_keys.service_id';

        if (!$container->hasParameter($serviceParameter)) {
            $container->setAlias('broadway_sensitive_serializer.aggregate_keys', 'broadway_sensitive_serializer.aggregate_keys.in_memory');

            return;
        }

        $serviceId = $container->getParameter($serviceParameter);

        Assert::string($serviceId);
        Assert::notEmpty($serviceId);

        $this->assertDefinitionImplementsInterface($container, $serviceId, AggregateKeys::class);

        $container->setAlias('broadway_sensitive_serializer.aggregate_keys', new Alias($serviceId, true));
    }
}
