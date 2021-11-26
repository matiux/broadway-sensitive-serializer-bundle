<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\Configuration;

use InvalidArgumentException;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\Configuration;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\BroadwaySensitiveSerializerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Util\Path;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtension(): ExtensionInterface
    {
        return new BroadwaySensitiveSerializerExtension();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function no_config_allowed()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "strategy" under "broadway_sensitive_serializer" must be configured: Strategy to sensitize events payload');
        $this->assertProcessedConfigurationEquals(
            [
                'aggregate_keys' => ''
            ],
            [
                Path::testResources().'/no_config.yaml',
            ]
        );
    }

    /**
     * @test
     */
    public function empty_config_is_not_allowed()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "strategy" under "broadway_sensitive_serializer" must be configured: Strategy to sensitize events payload');
        $this->assertProcessedConfigurationEquals(
            [
                'aggregate_keys' => ''
            ],
            [
                Path::testResources().'/empty_config.yaml',
            ]
        );
    }
}
