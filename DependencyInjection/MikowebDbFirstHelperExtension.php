<?php

/*
 * (c) Rafał Mikołajun <root@rmweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * DbFirstHelper Extension.
 *
 * @author Rafał Mikołajun <root@rmweb.pl>
 * @package mikoweb/db-first-helper-bundle
 */
class MikowebDbFirstHelperExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $root = Configuration::ROOT_NAME;

        $container->setParameter("$root.base_path", $config['base_path']);
        $container->setParameter("$root.entity_folder", $config['entity_folder']);
        $container->setParameter("$root.base_namespace", $config['base_namespace']);
        $container->setParameter("$root.force_update", $config['force_update']);
        $container->setParameter("$root.connection", $config['connection']);
        $container->setParameter("$root.generate_getters_setters", $config['generate_getters_setters']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
