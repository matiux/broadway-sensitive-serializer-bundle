<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\CompilerPass;

use InvalidArgumentException;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterPartialStrategyCompilerPass;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterStrategyCompilerPass;
use Matiux\Broadway\SensitiveSerializer\Serializer\Strategy\PayloadSensitizer;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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
    public function it_throws_when_partial_sensitizer_is_not_subclass_of_abstract_parent_class(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(
            sprintf('Service "%s" must extend abstract class "%s".', 'my_sensitizer', PayloadSensitizer::class)
        );

        $this->container->setParameter(
            RegisterStrategyCompilerPass::STRATEGY_ID,
            RegisterPartialStrategyCompilerPass::STRATEGY_NAME
        );

        $mySensitizer = new Definition(\stdClass::class);
        $mySensitizer->addTag('broadway.sensitive_serializer.partial');
        $this->setDefinition('my_sensitizer', $mySensitizer);

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

        $mySensitizer = new Definition(MyPartialSensitizer::class);
        $mySensitizer->addTag('broadway.sensitive_serializer.partial');
        $this->setDefinition('my_sensitizer', $mySensitizer);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'broadway_sensitive_serializer.strategy.partial.registry',
            0,
            [
                new Reference('my_sensitizer'),
            ]
        );

        $this->assertContainerBuilderHasAlias(
            'broadway_sensitive_serializer.strategy',
            'broadway_sensitive_serializer.strategy.partial'
        );
    }
}

class MyPartialSensitizer extends PayloadSensitizer
{
    protected function generateSensitizedPayload(string $decryptedAggregateKey): array
    {
        return [];
    }

    protected function generateDesensitizedPayload(string $decryptedAggregateKey): array
    {
        return [];
    }

    public function supports($subject): bool
    {
        return true;
    }
}
