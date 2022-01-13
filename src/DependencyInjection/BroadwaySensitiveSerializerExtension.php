<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Exception;
use LogicException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Webmozart\Assert\Assert;

class BroadwaySensitiveSerializerExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $container = $this->loadServices($container);

        $this->bindKeyGenerator($mergedConfig, $container);
        $this->bindDataManager($mergedConfig, $container);
        $this->bindAggregateMasterKey($mergedConfig, $container);
        $this->bindSensitiveSerializerStrategy($mergedConfig, $container);

        if (isset($mergedConfig['aggregate_keys'])) {
            $container->setParameter('broadway_sensitive_serializer.aggregate_keys.service_id', (string) $mergedConfig['aggregate_keys']);
        }
    }

    /**
     * @param ContainerBuilder $container
     *
     * @throws Exception
     *
     * @return ContainerBuilder
     */
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
                throw new LogicException('Invalid key generator strategy');
        }
    }

    private function bindDataManager(array $config, ContainerBuilder $container): void
    {
        Assert::isArray($config['data_manager']);

        switch ($config['data_manager']['name']) {
            case 'AES256':
                $this->bindAES256DataManager($config, $container);

                break;
            default:
                throw new LogicException('Invalid data manager');
        }
    }

    private function bindAES256DataManager(array $config, ContainerBuilder $container): void
    {
        Assert::isArray($config['data_manager']);
        Assert::isArray($config['data_manager']['parameters']);
        Assert::isArray($config['data_manager']['parameters']['AES256']);

        /** @var array{key: null|string, iv: null|string, iv_encoding: bool} $AES256Config */
        $AES256Config = $config['data_manager']['parameters']['AES256'];

        $container->setParameter('matiux.broadway.sensitive_serializer.key', $AES256Config['key']);
        $container->setParameter('matiux.broadway.sensitive_serializer.iv', $AES256Config['iv']);
        $container->setParameter('matiux.broadway.sensitive_serializer.iv_encoding', $AES256Config['iv_encoding']);

        $container->setAlias('broadway_sensitive_serializer.data_manager', 'broadway_sensitive_serializer.data_manager.aes256');
    }

    private function bindAggregateMasterKey(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('matiux.broadway.sensitive_serializer.aggregate_master_key', (string) $config['aggregate_master_key']);
    }

    private function bindSensitiveSerializerStrategy(array $config, ContainerBuilder $container): void
    {
        Assert::isArray($config['strategy']);

        $strategyName = (string) $config['strategy']['name'];

        $container->setParameter(RegisterStrategyCompilerPass::STRATEGY_ID, $strategyName);

        switch ($strategyName) {
            case RegisterWholeStrategyCompilerPass::STRATEGY_NAME:
                $this->bindWholeStrategy($config, $container);

                break;
            case RegisterCustomStrategyCompilerPass::STRATEGY_NAME:
                $this->bindCustomStrategy($config, $container);

                break;
            default:
                throw new LogicException('Invalid strategy');
        }
    }

    private function bindWholeStrategy(array $config, ContainerBuilder $container): void
    {
        Assert::isArray($config['strategy']);
        Assert::isArray($config['strategy']['parameters']);

        /** @var array{aggregate_key_auto_creation: bool, excluded_keys: list<string>, excluded_id_key: string, events: list<string>} $wholeStrategyConfig */
        $wholeStrategyConfig = $config['strategy']['parameters'][RegisterWholeStrategyCompilerPass::STRATEGY_NAME];

        $container->setParameter(
            'matiux.broadway.sensitive_serializer.strategy.aggregate_key_auto_creation',
            $wholeStrategyConfig['aggregate_key_auto_creation']
        );

        $container->setParameter(
            'matiux.broadway.sensitive_serializer.strategy.excluded_keys',
            $wholeStrategyConfig['excluded_keys']
        );

        $container->setParameter(
            'matiux.broadway.sensitive_serializer.strategy.excluded_id_key',
            $wholeStrategyConfig['excluded_id_key']
        );

        $container->setParameter(
            RegisterWholeStrategyCompilerPass::STRATEGY_WHOLE_EVENTS_PARAMETER,
            $wholeStrategyConfig['events']
        );
    }

    private function bindCustomStrategy(array $config, ContainerBuilder $container): void
    {
        Assert::isArray($config['strategy']);
        Assert::isArray($config['strategy']['parameters']);

        /** @var array{aggregate_key_auto_creation: bool} $customStrategyConfig */
        $customStrategyConfig = $config['strategy']['parameters'][RegisterCustomStrategyCompilerPass::STRATEGY_NAME];

        $container->setParameter(
            'matiux.broadway.sensitive_serializer.strategy.aggregate_key_auto_creation',
            $customStrategyConfig['aggregate_key_auto_creation']
        );
    }
}
