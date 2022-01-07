<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use LogicException;
use Matiux\Broadway\SensitiveSerializer\Serializer\Strategy\WholePayloadStrategy\WholePayloadSensitizerRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterWholeStrategyCompilerPass extends RegisterStrategyCompilerPass
{
    public const STRATEGY_NAME = 'whole';
    public const STRATEGY_WHOLE_EVENTS_PARAMETER = 'matiux.broadway.sensitive_serializer.strategy.whole.events';

    protected function doProcess(ContainerBuilder $container): void
    {
        $this->registerRegistry($container);

        $container->setAlias('broadway_sensitive_serializer.strategy', 'broadway_sensitive_serializer.strategy.whole');
    }

    private function registerRegistry(ContainerBuilder $container): void
    {
        if (!$container->hasParameter(self::STRATEGY_WHOLE_EVENTS_PARAMETER)) {
            throw new LogicException(sprintf('`%s` is not set', self::STRATEGY_WHOLE_EVENTS_PARAMETER));
        }

        $events = $container->getParameter(self::STRATEGY_WHOLE_EVENTS_PARAMETER);

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
