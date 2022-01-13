<?php

declare(strict_types=1);

namespace Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\DependencyInjection;

use Matiux\Broadway\SensitiveSerializer\DataManager\Domain\Aggregate\AggregateKeys;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress PossiblyNullReference, PossiblyUndefinedMethod, MixedMethodCall, UnusedMethodCall
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('broadway_sensitive_serializer');

        $rootNode = $this->obtainRootNode($treeBuilder);

        $this->configureStrategy($rootNode);
        $this->configureKeyGenerator($rootNode);
        $this->configureDataManager($rootNode);
        $this->configureAggregateKeys($rootNode);
        $this->setAggregateMasterKey($rootNode);

        return $treeBuilder;
    }

    private function obtainRootNode(TreeBuilder $treeBuilder): ArrayNodeDefinition
    {
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } elseif (method_exists($treeBuilder, 'root')) {
            /**
             * BC layer for symfony/config 4.1 and older.
             *
             * @var ArrayNodeDefinition|NodeDefinition
             */
            $rootNode = $treeBuilder->root('broadway_sensitive_serializer');
        } else {
            throw new \LogicException("Can't obtain root node");
        }

        Assert::isInstanceOf($rootNode, ArrayNodeDefinition::class);

        return $rootNode;
    }

    private function configureStrategy(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()
            ->arrayNode('strategy')
                ->isRequired()->info('Strategy configuration to sensitize events payload')
                ->children()
                    ->enumNode('name')
                    ->values([
                        RegisterWholeStrategyCompilerPass::STRATEGY_NAME,
                        RegisterCustomStrategyCompilerPass::STRATEGY_NAME,
                        RegisterPartialStrategyCompilerPass::STRATEGY_NAME,
                    ])
                    ->isRequired()
                    ->info('Strategy name to sensitize events payload. Use whole, partial or custom.')
                ->end()
                ->arrayNode('parameters')
                    ->info('Configuration for specific strategy.')
                ->end()
                ->end()
            ->end()
        ->end();

        $this->expandName($rootNode->find('strategy'));
        $this->expandParameters($rootNode->find('strategy'));

        $this->addWholeStrategyParameters($rootNode);
        $this->addPartialStrategyParameters($rootNode);
        $this->addCustomStrategyParameters($rootNode);
    }

    private function addWholeStrategyParameters(ArrayNodeDefinition $node): void
    {
        /** @var ArrayNodeDefinition $strategyParameterNode */
        $strategyParameterNode = $node->find('strategy.parameters');
        
        $strategyParameterNode->children()
            ->arrayNode(RegisterWholeStrategyCompilerPass::STRATEGY_NAME)
                ->info('Strategy to sensitize all keys in payload but the id')
                ->children()
                    ->booleanNode('aggregate_key_auto_creation')
                        ->defaultTrue()
                        ->info('Choose whether to use auto creation for the aggregate_key. Default true')
                    ->end()
                    ->scalarNode('excluded_id_key')
                        ->defaultValue('id')
                        ->info('The key to the id to be excluded from sensitization. Default `id`')
                    ->end()
                    ->arrayNode('excluded_keys')
                        ->scalarPrototype()->end()
                        ->defaultValue(['occurred_at'])
                        ->info('Keys that you want to exclude from sensitization. Default `[occurred_at]`')
                    ->end()
                    ->arrayNode('events')
                        ->scalarPrototype()->end()
                        ->info('List of events to sensitize')
                        ->isRequired()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
    
    private function addPartialStrategyParameters(ArrayNodeDefinition $node): void
    {
        /** @var ArrayNodeDefinition $strategyParameterNode */
        $strategyParameterNode = $node->find('strategy.parameters');
        
        $strategyParameterNode->children()
            ->arrayNode(RegisterPartialStrategyCompilerPass::STRATEGY_NAME)
                ->info('Strategy to sensitize TODO')
                ->children()
                    ->booleanNode('aggregate_key_auto_creation')
                        ->defaultTrue()
                        ->info('Choose whether to use auto creation for the aggregate_key. Default true')
                    ->end()
                    ->arrayNode('events')
                        ->arrayPrototype()->scalarPrototype()->end()
                        ->info('List of events to sensitize')
                        ->isRequired()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
    
    private function addCustomStrategyParameters(ArrayNodeDefinition $node): void
    {
        /** @var ArrayNodeDefinition $strategyParameterNode */
        $strategyParameterNode = $node->find('strategy.parameters');
        
        $strategyParameterNode->children()
            ->arrayNode(RegisterCustomStrategyCompilerPass::STRATEGY_NAME)
                ->info('Strategy for payload sensitization in a custom way')
                ->children()
                    ->booleanNode('aggregate_key_auto_creation')
                        ->defaultTrue()
                        ->info('Choose whether to use auto creation for the aggregate_key. Default true')
                    ->end()
                ->end()
            ->end()
        ->end();
    }
    
    private function configureKeyGenerator(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()
            ->enumNode('key_generator')
                ->values(['open-ssl'])
                ->info('Key generator strategy to creare Aggregate keys')
                ->isRequired()
                ->defaultValue('open-ssl')
            ->end()
        ->end();
    }

    private function configureDataManager(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()
            ->arrayNode('data_manager')
                ->info('Concrete class to handle data encryption and decryption')
                ->isRequired()
                ->children()
                    ->enumNode('name')
                        ->values(['AES256'])
                        ->defaultValue('AES256')
                        ->info('Data manager strategy name')
                        ->isRequired()
                    ->end()
                    ->arrayNode('parameters')
                        ->info('Configuration for specific strategy data manager')
                    ->end()
                ->end()
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
                ->info(sprintf('A service definition id implementing %s',AggregateKeys::class))
                ->isRequired()
            ->end()
        ->end();
    }

    private function expandName(NodeDefinition $node): void
    {
        $node
            ->beforeNormalization()
            ->ifString()
            ->then(function (string $v) {
                return [
                    'name' => $v
                ];
            })
            ->end()
        ;
    }

    private function expandParameters(NodeDefinition $node): void
    {
        $node
            ->beforeNormalization()
            ->ifArray()
            ->then(function (array $originalConfig) {

                $normalizedConfig = [
                    'name' => $originalConfig['name'],
                ];

                if (isset($originalConfig['parameters'])) {

                    Assert::isArray($originalConfig['parameters']);

                    $normalizedConfig['parameters'] = $originalConfig['parameters'];

                } else {

                    /** @var int|string|bool $value */
                    foreach ($originalConfig as $key => $value) {

                        if (in_array($key, ['name', 'parameters'])) {
                            continue;
                        }

                        $normalizedConfig['parameters'][(string)$originalConfig['name']][$key] = $value;
                    }
                }

                return $normalizedConfig;
            })
            ->end()
        ;
    }

    private function addAES256Parameters(ArrayNodeDefinition $node): void
    {
        $nodeChildren = $node->find('data_manager.parameters');

        Assert::isInstanceOf($nodeChildren,ArrayNodeDefinition::class);

        $nodeChildren->children()
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
                    ->booleanNode('iv_encoding')
                        ->info('Encrypt the iv and is appends to encrypted value. It makes sense to set it to true if the iv option is set to null')
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    private function setAggregateMasterKey(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->scalarNode('aggregate_master_key')
                ->isRequired()
                ->info('Master key to encrypt the keys of aggregates. Get it from an external service or environment variable')
            ->end()
        ->end();
    }
}