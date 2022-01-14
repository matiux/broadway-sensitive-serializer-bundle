<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Extension;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\BroadwaySensitiveSerializerExtension;
use Matiux\Broadway\SensitiveSerializer\DataManager\Infrastructure\Domain\Service\OpenSSLKeyGenerator;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;
use Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Util\Path;

class KeyGeneratorExtensionTest extends AbstractExtensionTestCase
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
    public function it_registers_the_key_generator_service_when_configured(): void
    {
        $shortCustomConfig = (array) Yaml::parseFile(Path::testResources().'/short_custom_config.yaml');

        $this->load(
            (array) $shortCustomConfig[(string) array_key_first($shortCustomConfig)]
        );

        $this->assertContainerBuilderHasAlias(
            'broadway_sensitive_serializer.key_generator',
            'broadway_sensitive_serializer.key_generator.open_ssl'
        );

        $this->assertContainerBuilderHasService(
            'broadway_sensitive_serializer.key_generator',
            OpenSSLKeyGenerator::class
        );
    }

    /**
     * @test
     */
    public function it_throws_when_key_generator_has_invalid_value(): void
    {
        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage(
            'The value "md5" is not allowed for path "broadway_sensitive_serializer.key_generator". Permissible values: "open-ssl"'
        );

        $shortCustomConfig = (array) Yaml::parseFile(Path::testResources().'/short_custom_config.yaml');
        $shortCustomConfig = (array) $shortCustomConfig[(string) array_key_first($shortCustomConfig)];
        $shortCustomConfig['key_generator'] = 'md5';

        $this->load($shortCustomConfig);
    }
}
