<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle;

use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterAggregateKeysCompilerPass;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterPartialStrategyCompilerPass;
use Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection\RegisterWholeStrategyCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BroadwaySensitiveSerializerBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterAggregateKeysCompilerPass());
        $container->addCompilerPass(new RegisterWholeStrategyCompilerPass());
        $container->addCompilerPass(new RegisterPartialStrategyCompilerPass());
    }
}
