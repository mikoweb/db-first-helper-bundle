<?php

/*
 * (c) Rafał Mikołajun <root@rmweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Import database schema.
 *
 * @author Rafał Mikołajun <root@mikoweb.pl>
 * @package mikoweb/db-first-helper-bundle
 */
abstract class ImportDatabaseCommandAbstract extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $this->getBundleDirectory();
        $fs = new Filesystem();
        $entityDir = $directory . '/Entity';
        $tableDir = $directory . '/Table';

        if ($fs->exists($entityDir)) {
            $fs->rename($entityDir, $entityDir . '_backup');
        }

        $fs->remove($tableDir);

        $import = $this->getApplication()->find('doctrine:mapping:import');
        $returnCode = $import->run(new ArrayInput([
            'command' => 'doctrine:mapping:import',
            'bundle' => $this->getBundleName(),
            'mapping-type' => 'annotation',
            '--force' => true,
        ]), $output);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Import fail.');
        }

        $fs->rename($entityDir, $tableDir);

        if ($fs->exists($entityDir . '_backup')) {
            $fs->rename($entityDir . '_backup', $entityDir);
        }

        foreach (glob($tableDir . '/*.php') as $fileName) {
            $content = file_get_contents($fileName);
            $content = str_replace(
                'namespace ' . $this->getBundleNamespace() . '\Entity',
                'namespace ' . $this->getBundleNamespace() . '\Table',
                $content
            );

            $content = str_replace(
                'private $',
                'protected $',
                $content
            );

            $content = str_replace(
                'targetEntity="',
                'targetEntity="' . $this->getBundleNamespace() . '\\Entity\\',
                $content
            );

            $content = $this->transformClassContent($content);

            file_put_contents($fileName, $content);
        }

        $this->doAfterImport($entityDir, $tableDir);
        $output->writeln('<info>Import complete.</info>');
    }

    /**
     * Here you can overwrite the content of class.
     *
     * @param string $content
     *
     * @return string
     */
    protected function transformClassContent($content)
    {
        return $content;
    }

    /**
     * You can do something after import.
     *
     * @param string $entityDir
     * @param string $tableDir
     */
    protected function doAfterImport($entityDir, $tableDir)
    {
    }

    /**
     * @return string
     */
    protected function getBundleDirectory()
    {
        return $this->getContainer()->get('kernel')->getRootDir() . '/../'
            . $this->getContainer()->getParameter('mikoweb_db_first_helper.bundle_directory');
    }

    /**
     * @return string
     */
    protected function getBundleName()
    {
        return $this->getContainer()->getParameter('mikoweb_db_first_helper.bundle_name');
    }

    /**
     * @return string
     */
    protected function getBundleNamespace()
    {
        return $this->getContainer()->getParameter('mikoweb_db_first_helper.bundle_namespace');
    }
}
