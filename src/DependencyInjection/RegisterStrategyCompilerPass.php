<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Broadway\Bundle\BroadwayBundle\DependencyInjection\CompilerPass;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webmozart\Assert\Assert;

abstract class RegisterStrategyCompilerPass extends CompilerPass
{
    public const STRATEGY_ID = 'matiux.broadway.sensitive_serializer.strategy';

    abstract protected function strategyName(): string;

    abstract protected function doProcess(ContainerBuilder $container): void;

    public function process(ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress UndefinedDocblockClass
         */
        if (
            $container->hasParameter(self::STRATEGY_ID)
            && $this->strategyName() === $container->getParameter(self::STRATEGY_ID)
        ) {
            $this->registerValueSerializer($container);

            $this->doProcess($container);
        }
    }

    private function registerValueSerializer(ContainerBuilder $container): void
    {
        $serializer = $container->getParameter('matiux.broadway.sensitive_serializer.strategy.value_serializer');

        Assert::string($serializer);

        switch ($serializer) {
            case 'json':
                $container->setAlias(
                    'broadway_sensitive_serializer.strategy.value_serializer',
                    'broadway_sensitive_serializer.strategy.value_serializer.json'
                );

                break;
            default:
                throw new LogicException(sprintf('Invalid ValueSerializer name: %s', $serializer));
        }
    }
}
