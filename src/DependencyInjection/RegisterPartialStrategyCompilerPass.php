<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use LogicException;
use Matiux\Broadway\SensitiveSerializer\Serializer\Strategy\PartialStrategy\PartialPayloadSensitizerRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterPartialStrategyCompilerPass extends RegisterStrategyCompilerPass
{
    public const STRATEGY_NAME = 'partial';
    public const STRATEGY_PARTIAL_EVENTS_PARAMETER = 'matiux.broadway.sensitive_serializer.strategy.partial.events';

    protected function doProcess(ContainerBuilder $container): void
    {
        $this->registerRegistry($container);

        $container->setAlias('broadway_sensitive_serializer.strategy', 'broadway_sensitive_serializer.strategy.partial');
    }

    private function registerRegistry(ContainerBuilder $container): void
    {
        if (!$container->hasParameter(self::STRATEGY_PARTIAL_EVENTS_PARAMETER)) {
            throw new LogicException(sprintf('`%s` is not set', self::STRATEGY_PARTIAL_EVENTS_PARAMETER));
        }

        /**
         * @psalm-suppress UndefinedDocblockClass
         */
        $events = $container->getParameter(self::STRATEGY_PARTIAL_EVENTS_PARAMETER);

        $definition = new Definition(PartialPayloadSensitizerRegistry::class, [
            $events,
        ]);

        $container->setDefinition('broadway_sensitive_serializer.strategy.partial.registry', $definition);
    }

    protected function strategyName(): string
    {
        return self::STRATEGY_NAME;
    }
}
