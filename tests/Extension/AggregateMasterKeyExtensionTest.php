<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Extension;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\BroadwaySensitiveSerializerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Yaml\Yaml;
use Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Util\Path;

class AggregateMasterKeyExtensionTest extends AbstractExtensionTestCase
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
    public function it_registers_the_aggregate_master_key_parameter_when_configured(): void
    {
        $shortPartialConfig = (array) Yaml::parseFile(Path::testResources().'/short_partial_config.yaml');

        $this->load(
            (array) $shortPartialConfig[(string) array_key_first($shortPartialConfig)]
        );

        $this->assertContainerBuilderHasParameter(
            'matiux.broadway.sensitive_serializer.aggregate_master_key',
            'm4$t3rS3kr3tk31'
        );
    }
}
