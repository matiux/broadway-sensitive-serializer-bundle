<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use InvalidArgumentException;
use Matiux\Broadway\SensitiveSerializer\Serializer\Strategy\CustomStrategy\CustomPayloadSensitizerRegistry;
use Matiux\Broadway\SensitiveSerializer\Serializer\Strategy\PayloadSensitizer;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterCustomStrategyCompilerPass extends RegisterStrategyCompilerPass
{
    public const STRATEGY_NAME = 'custom';

    protected function strategyName(): string
    {
        return self::STRATEGY_NAME;
    }

    /**
     * @param ContainerBuilder $container
     *
     * @throws ReflectionException
     */
    protected function doProcess(ContainerBuilder $container): void
    {
        $this->registerRegistry($container);

        $container->setAlias('broadway_sensitive_serializer.strategy', 'broadway_sensitive_serializer.strategy.custom');
    }

    /**
     * @param ContainerBuilder $container
     *
     * @throws ReflectionException
     */
    private function registerRegistry(ContainerBuilder $container): void
    {
        $serializers = [];

        /** @var PayloadSensitizer[] $a */
        $sensitizers = $container->findTaggedServiceIds('broadway.sensitive_serializer.custom');

        /**
         * @var class-string $id
         */
        foreach ($sensitizers as $id => $_) {
            $def = $container->getDefinition($id);

            /**
             * Definition getClass can return a parameter.
             *
             * @var class-string $class
             */
            $class = $container->getParameterBag()->resolveValue($def->getClass());

            $refClass = new ReflectionClass($class);

            if (!$refClass->isSubclassOf(PayloadSensitizer::class)) {
                throw new InvalidArgumentException(sprintf('Service "%s" must extend abstract class "%s".', $id, PayloadSensitizer::class));
            }

            $serializers[] = new Reference($id);
        }

        $definition = new Definition(CustomPayloadSensitizerRegistry::class, [
            $serializers,
        ]);

        $container->setDefinition('broadway_sensitive_serializer.strategy.custom.registry', $definition);
    }
}
