<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Matiux\Broadway\SensitiveSerializer\Serializer\Strategy\WholePayloadStrategy\WholePayloadSensitizerRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterWholeStrategyCompilerPass extends RegisterStrategyCompilerPass
{
    public const STRATEGY_NAME = 'whole';

    protected function doProcess(ContainerBuilder $container): void
    {
        $this->registerRegistry($container);

        $container->setAlias('broadway_sensitive_serializer.strategy', 'broadway_sensitive_serializer.strategy.whole');
    }

    private function registerRegistry(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('matiux.broadway.sensitive_serializer.strategy.whole.events')) {
            throw new \LogicException('`broadway.sensitive_serializer.strategy.whole.events` is not set');
        }

        $events = $container->getParameter('matiux.broadway.sensitive_serializer.strategy.whole.events');

        $definition = new Definition(WholePayloadSensitizerRegistry::class, [
            $events,
        ]);

        $container->setDefinition('broadway_sensitive_serializer.strategy.whole.registry', $definition);
    }

    protected function strategyName(): string
    {
        return self::STRATEGY_NAME;
    }
}
