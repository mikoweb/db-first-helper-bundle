<?php

/*
 * (c) Rafał Mikołajun <rafal@mikoweb.pl>
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
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package mikoweb/db-first-helper-bundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mikoweb_db_first_helper');

        $rootNode
            ->children()
                ->scalarNode('bundle_directory')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('bundle_name')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('bundle_namespace')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
