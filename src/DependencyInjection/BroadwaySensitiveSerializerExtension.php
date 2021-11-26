<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Service\KeyGenerator;
use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Service\SensitiveDataManager;
use Matiux\Broadway\SensitiveSerializer\DataManager\Infrastructure\Domain\Service\AES256SensitiveDataManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Webmozart\Assert\Assert;

class BroadwaySensitiveSerializerExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container = $this->loadServices($container);

        $this->bindKeyGenerator($mergedConfig, $container);
        $this->bindDataManager($mergedConfig, $container);
        $this->bindAggregateMasterKey($mergedConfig, $container);

        if (isset($mergedConfig['aggregate_keys'])) {
            $container->setParameter('broadway_sensitive_serializer.aggregate_keys.service_id', $mergedConfig['aggregate_keys']);
        }
    }

    private function loadServices(ContainerBuilder $container): ContainerBuilder
    {

        $locator = new FileLocator(__DIR__.'/../Resources');
        $loader = new DirectoryLoader($container, $locator);
        $resolver = new LoaderResolver([
            new YamlFileLoader($container, $locator),
            $loader,
        ]);
        $loader->setResolver($resolver);

        $loader->load('config');

        return $container;
    }

    private function bindKeyGenerator(array $config, ContainerBuilder $container): void
    {
        switch ($config['key_generator']) {
            case 'open-ssl':
                $container->setAlias('broadway_sensitive_serializer.key_generator', 'broadway_sensitive_serializer.key_generator.open_ssl');
                break;
            default:
                throw new \LogicException('Invalid key generator strategy');
        }
    }

    private function bindDataManager(array $config, ContainerBuilder $container): void
    {
        switch($config['data_manager']['name']) {
            case 'AES256':
                $this->bindAES256DataManager($config, $container);
                break;
            default:
                throw new \LogicException('Invalid data manager');
        }
    }

    private function bindAES256DataManager(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('matiux.broadway.sensitive_serializer.key', $config['data_manager']['parameters']['AES256']['key']);
        $container->setParameter('matiux.broadway.sensitive_serializer.iv', $config['data_manager']['parameters']['AES256']['iv']);
        $container->setParameter('matiux.broadway.sensitive_serializer.iv_encoding', $config['data_manager']['parameters']['AES256']['iv_encoding']);
        
        $container->setAlias(SensitiveDataManager::class, 'broadway_sensitive_serializer.data_manager.aes256');
    }

    private function bindAggregateMasterKey(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('matiux.broadway.sensitive_serializer.aggregate_master_key', $config['aggregate_master_key']);
    }
}
