<?php

/*
 * (c) Rafał Mikołajun <root@rmweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * User Configuration.
 *
 * @author Rafał Mikołajun <root@rmweb.pl>
 * @package mikoweb/db-first-helper-bundle
 */
class Configuration implements ConfigurationInterface
{
    const ROOT_NAME = 'mikoweb_db_first_helper';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ROOT_NAME);

        $rootNode
            ->children()
                ->scalarNode('base_path')
                    ->cannotBeEmpty()
                    ->defaultValue('src')
                ->end()
                ->scalarNode('entity_folder')
                    ->cannotBeEmpty()
                    ->defaultValue('Table')
                ->end()
                ->scalarNode('base_namespace')
                    ->cannotBeEmpty()
                    ->defaultValue('App')
                ->end()
                ->scalarNode('force_update')
                    ->cannotBeEmpty()
                    ->defaultTrue()
                ->end()
                ->scalarNode('connection')
                    ->cannotBeEmpty()
                    ->defaultValue('default')
                ->end()
                ->booleanNode('generate_getters_setters')
                    ->defaultTrue()
                ->end()
                ->booleanNode('make_repositories')
                    ->defaultTrue()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
