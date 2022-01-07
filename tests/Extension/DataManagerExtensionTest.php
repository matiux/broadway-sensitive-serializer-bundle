<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Extension;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\BroadwaySensitiveSerializerExtension;
use Matiux\Broadway\SensitiveSerializer\DataManager\Infrastructure\Domain\Service\AES256SensitiveDataManager;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;
use Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Util\Path;

class DataManagerExtensionTest extends AbstractExtensionTestCase
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
    public function it_registers_the_data_manager_service_when_configured(): void
    {
        $shortPartialConfig = (array) Yaml::parseFile(Path::testResources().'/short_partial_config.yaml');

        $this->load(
            (array) $shortPartialConfig[(string) array_key_first($shortPartialConfig)]
        );

        $this->assertContainerBuilderHasParameter('matiux.broadway.sensitive_serializer.key', null);
        $this->assertContainerBuilderHasParameter('matiux.broadway.sensitive_serializer.iv', null);
        $this->assertContainerBuilderHasParameter('matiux.broadway.sensitive_serializer.iv_encoding', true);

        $this->assertContainerBuilderHasAlias(
            'broadway_sensitive_serializer.data_manager',
            'broadway_sensitive_serializer.data_manager.aes256'
        );

        $this->assertContainerBuilderHasService(
            'broadway_sensitive_serializer.data_manager',
            AES256SensitiveDataManager::class
        );
    }

    /**
     * @test
     */
    public function it_throws_when_data_manager_has_invalid_value(): void
    {
        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('Unrecognized option "md5" under "broadway_sensitive_serializer.data_manager.parameters');

        $shortPartialConfig = (array) Yaml::parseFile(Path::testResources().'/short_partial_config.yaml');
        $shortPartialConfig = (array) $shortPartialConfig[(string) array_key_first($shortPartialConfig)];

        self::assertIsArray($shortPartialConfig['data_manager']);

        $shortPartialConfig['data_manager']['name'] = 'md5';

        $this->load($shortPartialConfig);
    }
}
