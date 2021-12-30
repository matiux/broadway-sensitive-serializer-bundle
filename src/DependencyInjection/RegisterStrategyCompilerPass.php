<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Broadway\Bundle\BroadwayBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class RegisterStrategyCompilerPass extends CompilerPass
{
    public const STRATEGY_ID = 'matiux.broadway.sensitive_serializer.strategy';

    abstract protected function strategyName(): string;

    abstract protected function doProcess(ContainerBuilder $container): void;

    public function process(ContainerBuilder $container): void
    {
        if (
            !$container->hasParameter(self::STRATEGY_ID)
            || $this->strategyName() != $container->getParameter(self::STRATEGY_ID)
        ) {
            return;
        }

        $this->doProcess($container);
    }
}
