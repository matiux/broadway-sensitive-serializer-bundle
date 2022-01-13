<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Extension;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\BroadwaySensitiveSerializerExtension;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterPartialStrategyCompilerPass;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterWholeStrategyCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Yaml\Yaml;
use Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Util\Path;

class StrategyExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new BroadwaySensitiveSerializerExtension(),
        ];
    }

    /**
     * @test
     */
    public function it_registers_whole_strategy_when_configured(): void
    {
        $shortWholeConfig = (array) Yaml::parseFile(Path::testResources().'/short_whole_config.yaml');

        $this->load(
            (array) $shortWholeConfig[(string) array_key_first($shortWholeConfig)]
        );

        $this->assertContainerBuilderHasParameter(
            'matiux.broadway.sensitive_serializer.strategy.aggregate_key_auto_creation',
            true
        );

        $this->assertContainerBuilderHasParameter(
            'matiux.broadway.sensitive_serializer.strategy.excluded_keys',
            ['occurred_at']
        );

        $this->assertContainerBuilderHasParameter(
            'matiux.broadway.sensitive_serializer.strategy.excluded_id_key',
            'id'
        );

        $this->assertContainerBuilderHasParameter(
            RegisterWholeStrategyCompilerPass::STRATEGY_WHOLE_EVENTS_PARAMETER,
            [
                'SensitiveUser\User\Domain\Event\AddressAdded',
                'SensitiveUser\User\Domain\Event\UserRegistered',
            ]
        );
    }

    /**
     * @test
     */
    public function it_registers_partial_strategy_when_configured(): void
    {
        $shortPartialConfig = (array) Yaml::parseFile(Path::testResources().'/short_partial_config.yaml');

        $this->load(
            (array) $shortPartialConfig[(string) array_key_first($shortPartialConfig)]
        );

        $this->assertContainerBuilderHasParameter(
            'matiux.broadway.sensitive_serializer.strategy.aggregate_key_auto_creation',
            true
        );

        $this->assertContainerBuilderHasParameter(
            RegisterPartialStrategyCompilerPass::STRATEGY_PARTIAL_EVENTS_PARAMETER,
            [
                'SensitiveUser\User\Domain\Event\UserRegistered' => ['email', 'surname'],
                'SensitiveUser\User\Domain\Event\AddressAdded' => ['address'],
            ]
        );
    }

    /**
     * @test
     */
    public function it_registers_custom_strategy_when_configured(): void
    {
        $shortCustomConfig = (array) Yaml::parseFile(Path::testResources().'/short_custom_config.yaml');

        $this->load(
            (array) $shortCustomConfig[(string) array_key_first($shortCustomConfig)]
        );

        $this->assertContainerBuilderHasParameter(
            'matiux.broadway.sensitive_serializer.strategy.aggregate_key_auto_creation',
            true
        );

        self::assertFalse($this->container->hasParameter('matiux.broadway.sensitive_serializer.strategy.excluded_keys'));
    }
}
