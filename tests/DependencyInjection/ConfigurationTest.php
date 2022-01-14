<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\BroadwaySensitiveSerializerExtension;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\Configuration;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Util\Path;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new BroadwaySensitiveSerializerExtension();
    }

    /**
     * @test
     */
    public function it_process_short_custom_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                'aggregate_master_key' => 'm4$t3rS3kr3tk31',
                'key_generator' => 'open-ssl',
                'aggregate_keys' => 'broadway_sensitive_serializer.aggregate_keys.in_memory',
                'data_manager' => [
                    'name' => 'AES256',
                    'parameters' => [
                        'AES256' => [
                            'key' => null,
                            'iv' => null,
                            'iv_encoding' => true,
                        ],
                    ],
                ],
                'strategy' => [
                    'name' => 'custom',
                    'parameters' => [
                        'custom' => [
                            'aggregate_key_auto_creation' => true,
                        ],
                    ],
                ],
            ],
            [
                Path::testResources().'/short_custom_config.yaml',
            ]
        );
    }

    /**
     * @test
     */
    public function it_process_short_whole_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                'aggregate_master_key' => 'm4$t3rS3kr3tk31',
                'key_generator' => 'open-ssl',
                'aggregate_keys' => 'broadway_sensitive_serializer.aggregate_keys.in_memory',
                'data_manager' => [
                    'name' => 'AES256',
                    'parameters' => [
                        'AES256' => [
                            'key' => null,
                            'iv' => null,
                            'iv_encoding' => true,
                        ],
                    ],
                ],
                'strategy' => [
                    'name' => 'whole',
                    'parameters' => [
                        'whole' => [
                            'aggregate_key_auto_creation' => true,
                            'excluded_id_key' => 'id',
                            'excluded_keys' => [
                                'occurred_at',
                            ],
                            'events' => [
                                'SensitiveUser\User\Domain\Event\AddressAdded',
                                'SensitiveUser\User\Domain\Event\UserRegistered',
                            ],
                        ],
                    ],
                ],
            ],
            [
                Path::testResources().'/short_whole_config.yaml',
            ]
        );
    }

    /**
     * @test
     */
    public function it_process_normalized_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                'aggregate_master_key' => 'm4$t3rS3kr3tk31',
                'key_generator' => 'open-ssl',
                'aggregate_keys' => 'broadway_sensitive_serializer.aggregate_keys.in_memory',
                'data_manager' => [
                    'name' => 'AES256',
                    'parameters' => [
                        'AES256' => [
                            'key' => null,
                            'iv' => null,
                            'iv_encoding' => true,
                        ],
                    ],
                ],
                'strategy' => [
                    'name' => 'custom',
                    'parameters' => [
                        'custom' => [
                            'aggregate_key_auto_creation' => true,
                        ],
                    ],
                ],
            ],
            [
                Path::testResources().'/normalized_config.yaml',
            ]
        );
    }
}
