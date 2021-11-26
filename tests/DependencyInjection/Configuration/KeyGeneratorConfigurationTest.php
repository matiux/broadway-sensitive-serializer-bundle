<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\Configuration;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\Configuration;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\BroadwaySensitiveSerializerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Util\Path;

class KeyGeneratorConfigurationTest extends AbstractExtensionConfigurationTestCase
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


//    /**
//     * @test
//     */
//    public function it_not_allows_the_key_generator_to_not_be_configured(): void
//    {
//        $this->expectException(InvalidConfigurationException::class);
//        $this->expectExceptionMessage('The child config "key_generator" under "broadway_sensitive_serializer" must be configured: Key generator strategy to creare Aggregate keys');
//
//        $this->assertProcessedConfigurationEquals(
//            [
//                [],
//            ],
//            [
//                Path::testResources().'/empty_broadway_sensitive_serializer.yaml',
//            ],
//        );
//    }
//
//    /**
//     * @test
//     */
//    public function it_allows_the_key_generator_to_be_configured(): void
//    {
//        $this->assertProcessedConfigurationEquals(
//            [
//                [
//                    'event_store' => 'my_key_generator',
//                ],
//            ],
//            [
//                Path::testResources().'/broadway_sensitive_serializer.yaml',
//            ],
//        );
//    }
}
