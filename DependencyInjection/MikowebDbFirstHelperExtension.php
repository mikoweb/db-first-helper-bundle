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

        $container->setParameter('mikoweb_db_first_helper.base_path', $config['base_path']);
        $container->setParameter('mikoweb_db_first_helper.entity_folder', $config['entity_folder']);
        $container->setParameter('mikoweb_db_first_helper.base_namespace', $config['base_namespace']);
        $container->setParameter('mikoweb_db_first_helper.force_update', $config['force_update']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
