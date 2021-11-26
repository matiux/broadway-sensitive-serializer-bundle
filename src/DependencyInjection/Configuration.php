<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Webmozart\Assert\Assert;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('broadway_sensitive_serializer');

        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('broadway_sensitive_serializer');
        }

//        $this->configureStrategy($rootNode);
        $this->configureKeyGenerator($rootNode);
        $this->configureDataManager($rootNode);
        $this->configureAggregateKeys($rootNode);
        $this->setAggregateMasterKey($rootNode);

        return $treeBuilder;
    }

    private function configureStrategy(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()
            ->arrayNode('strategy')
                ->isRequired()
                ->info('Strategy to sensitize events payload')
            ->end();
    }

    private function configureKeyGenerator(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()
            ->scalarNode('key_generator')
                ->info('Key generator strategy to creare Aggregate keys')
                ->isRequired()
                ->defaultValue('open-ssl')
            ->end();
    }

    private function configureDataManager(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()
            ->arrayNode('data_manager')
                ->info('Concrete class to handle data encryption and decryption')
                ->isRequired()
                ->children()
                    ->scalarNode('name')
                        ->defaultValue('AES256')
                        ->info('Data manager strategy name')
                        ->isRequired()
                    ->end()
                    ->arrayNode('parameters')
                        ->info('Configuration for specific strategy data manager')
                    ->end()
            ->end();

        $this->expandName($rootNode->find('data_manager'));
        $this->expandParameters($rootNode->find('data_manager'));

        $this->addAES256Parameters($rootNode);
    }

    private function configureAggregateKeys(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()
            ->scalarNode('aggregate_keys')
                ->info('a service definition id implementing Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Aggregate\AggregateKeys')
                ->isRequired()
            ->end();
    }

    private function expandName(ArrayNodeDefinition $node): void
    {
        $node
            ->beforeNormalization()
            ->ifString()
            ->then(function ($v) {
                return [
                    'name' => $v
                ];
            })
            ->end()
        ;
    }

    private function expandParameters(ArrayNodeDefinition $node): void
    {
        $node
            ->beforeNormalization()
            ->ifArray()
            ->then(function ($v) {
                $v2 = [
                    'name' => $v['name'],
                ];
                if (isset($v['parameters'])) {
                    $v2['parameters'] = $v['parameters'];
                }
                foreach ($v as $key => $value) {
                    if ($key =='name' || $key =='parameters') {
                        continue;
                    }
                    $v2['parameters'][$v['name']][$key] = $value;
                }
                return $v2;
            })
            ->end()
        ;
    }

    private function addAES256Parameters(ArrayNodeDefinition $node): void
    {
        $node->find('data_manager.parameters')
            ->children()
                ->arrayNode('AES256')
                    ->info('Protocol strategy to handle data encryption and decryption')
                    ->children()
                        ->scalarNode('key')
                            ->info('Encryption key to sensitize data. If null you will need to pass the key at runtime')
                            ->isRequired()
                        ->end()
                        ->scalarNode('iv')
                            ->info('Initialization vector. If null it will be generated internally and iv_encoding must be set to true')
                        ->end()
                        ->scalarNode('iv_encoding')
                            ->info('Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function setAggregateMasterKey(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->scalarNode('aggregate_master_key')
                ->isRequired()
                ->info('Master key to encrypt the keys of aggregates. Get it from an external service or environment variable')
            ->end();
    }
}
