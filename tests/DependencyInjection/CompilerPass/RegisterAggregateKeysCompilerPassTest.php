<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\CompilerPass;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterAggregateKeysCompilerPass;
use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Aggregate\AggregateKeys;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterAggregateKeysCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterAggregateKeysCompilerPass());
    }

    /**
     * @test
     */
    public function it_sets_the_aggregate_keys_repo_alias_to_in_memory_by_default(): void
    {
        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'broadway_sensitive_serializer.aggregate_keys',
            'broadway_sensitive_serializer.aggregate_keys.in_memory'
        );
    }

    /**
     * @test
     */
    public function it_sets_the_public_aggregate_keys_repo_alias(): void
    {
        $this->container->setParameter(
            'broadway_sensitive_serializer.aggregate_keys.service_id',
            'my_aggregate_keys_repo'
        );

        $this->setDefinition('my_aggregate_keys_repo', new Definition(AggregateKeys::class));
        $this->compile();

        $this->assertContainerBuilderHasAlias('broadway_sensitive_serializer.aggregate_keys', 'my_aggregate_keys_repo');
        $this->assertTrue($this->container->getAlias('broadway_sensitive_serializer.aggregate_keys')->isPublic());
    }
}
