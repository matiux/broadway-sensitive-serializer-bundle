<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\Extension;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\BroadwaySensitiveSerializerExtension;
use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Service\KeyGenerator;
use Matiux\Broadway\SensitiveSerializer\DataManager\Infrastructure\Domain\Service\OpenSSLKeyGenerator;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

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
    public function it_does_not_register_the_key_generator_service_when_not_configured(): void
    {
        $this->load([
            'aggregate_keys' => '',
        ]);

        $this->assertFalse($this->container->hasParameter('broadway_sensitive_serializer.key_generator.service_id'));
    }

    /**
     * @test
     */
    public function it_can_manage_open_ssl_driver()
    {
        $this->load([
            'aggregate_keys' => '',
            'key_generator' => 'open_ssl',
        ]);

        $this->assertContainerBuilderHasAlias(KeyGenerator::class, 'broadway_sensitive_serializer.key_generator.open_ssl');
        $this->assertContainerBuilderHasService('broadway_sensitive_serializer.key_generator.open_ssl', OpenSSLKeyGenerator::class);
    }
}
