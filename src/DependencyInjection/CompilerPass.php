<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class CompilerPass implements CompilerPassInterface
{
    protected function assertContainerHasDefinition(ContainerBuilder $container, string $definitionId): void
    {
        if (!$container->hasDefinition($definitionId)) {
            throw new InvalidArgumentException(sprintf('Service id "%s" could not be found in container', $definitionId));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $definitionId
     * @param class-string     $interface
     *
     * @throws ReflectionException
     */
    protected function assertDefinitionImplementsInterface(ContainerBuilder $container, string $definitionId, string $interface): void
    {
        $this->assertContainerHasDefinition($container, $definitionId);

        $definition = $container->getDefinition($definitionId);

        /** @var class-string $definitionClass */
        $definitionClass = $container->getParameterBag()->resolveValue($definition->getClass());

        $reflectionClass = new ReflectionClass($definitionClass);

        if (!$reflectionClass->implementsInterface($interface)) {
            throw new InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $definitionClass, $interface));
        }
    }
}
