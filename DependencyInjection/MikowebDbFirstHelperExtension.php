<?php

/*
 * (c) Rafał Mikołajun <rafal@mikoweb.pl>
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
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
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
        $container->setParameter('mikoweb_db_first_helper.bundle_directory', $config['bundle_directory']);
        $container->setParameter('mikoweb_db_first_helper.bundle_name', $config['bundle_name']);
        $container->setParameter('mikoweb_db_first_helper.bundle_namespace', $config['bundle_namespace']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
