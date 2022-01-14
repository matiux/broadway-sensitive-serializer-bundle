<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\CompilerPass;

use LogicException;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterPartialStrategyCompilerPass;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterStrategyCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterPartialStrategyCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterPartialStrategyCompilerPass());
    }

    /**
     * @test
     */
    public function it_does_nothing_if_the_strategy_is_not_registered(): void
    {
        $this->compile();

        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     */
    public function it_throws_when_events_are_not_set(): void
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage(
            sprintf('`%s` is not set', RegisterPartialStrategyCompilerPass::STRATEGY_PARTIAL_EVENTS_PARAMETER)
        );

        $this->container->setParameter(
            RegisterStrategyCompilerPass::STRATEGY_ID,
            RegisterPartialStrategyCompilerPass::STRATEGY_NAME
        );

        $this->compile();
    }

    /**
     * @test
     */
    public function it_registers_partial_strategy_registry_with_supported_events(): void
    {
        $this->container->setParameter(
            RegisterStrategyCompilerPass::STRATEGY_ID,
            RegisterPartialStrategyCompilerPass::STRATEGY_NAME
        );
        $this->container->setParameter(
            RegisterPartialStrategyCompilerPass::STRATEGY_PARTIAL_EVENTS_PARAMETER,
            ['Event\Registered']
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'broadway_sensitive_serializer.strategy.partial.registry',
            0,
            ['Event\Registered']
        );

        $this->assertContainerBuilderHasAlias(
            'broadway_sensitive_serializer.strategy',
            'broadway_sensitive_serializer.strategy.partial'
        );
    }
}
