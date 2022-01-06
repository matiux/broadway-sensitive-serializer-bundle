<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\CompilerPass;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterKeyGeneratorCompilerPass;
use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Service\KeyGenerator;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterKeyGeneratorCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterKeyGeneratorCompilerPass());
    }

    /**
     * @test
     */
    public function it_sets_the_key_generator_alias_to_open_ssl_by_default(): void
    {
        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'broadway_sensitive_serializer.key_generator',
            'broadway_sensitive_serializer.key_generator.open_ssl'
        );
    }

    /**
     * @test
     */
    public function it_sets_the_public_key_generator_alias(): void
    {
        $this->container->setParameter('broadway_sensitive_serializer.key_generator.service_id', 'my_key_generator');

        $this->setDefinition('my_key_generator', new Definition(KeyGenerator::class));

        $this->compile();

        $this->assertContainerBuilderHasAlias('broadway_sensitive_serializer.key_generator', 'my_key_generator');
        $this->assertTrue($this->container->getAlias('broadway_sensitive_serializer.key_generator')->isPublic());
    }

    /**
     * @test
     */
    public function it_throws_when_configured_key_generator_has_no_definition(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Service id "my_key_generator" could not be found in container');
        $this->container->setParameter('broadway_sensitive_serializer.key_generator.service_id', 'my_key_generator');

        $this->compile();
    }

    /**
     * @test
     */
    public function it_throws_when_configured_key_generator_does_not_implement_key_generator_interface(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage(sprintf('Service "stdClass" must implement interface "%s".', KeyGenerator::class));

        $this->container->setParameter('broadway_sensitive_serializer.key_generator.service_id', 'my_key_generator');

        $this->setDefinition('my_key_generator', new Definition(\stdClass::class));

        $this->compile();
    }
}
