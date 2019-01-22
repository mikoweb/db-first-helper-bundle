<?php

/*
 * (c) Rafał Mikołajun <root@rmweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\Command;

use Mikoweb\Bundle\DbFirstHelperBundle\DependencyInjection\Configuration;
use Mikoweb\Bundle\DbFirstHelperBundle\EntityTransformer\GettersSettersTransformer;
use Mikoweb\Bundle\DbFirstHelperBundle\EntityTransformer\PrivateTransformer;
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
        if ($this->isForceUpdate()) {
            $fs = new Filesystem();
            $fs->remove($this->getEntityPath());
        }

        $import = $this->getApplication()->find('doctrine:mapping:import');

        $returnCode = $import->run(new ArrayInput([
            'command' => 'doctrine:mapping:import',
            'name' => $this->getEntityNamespace(),
            'mapping-type' => 'annotation',
            '--path' => $this->getEntityPath(),
            '--force' => $this->isForceUpdate(),
            '--em' => $this->getConnection(),
        ]), $output);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Import fail.');
        }

        foreach (glob($this->getEntityPath() . '/*.php') as $fileName) {
            $content = file_get_contents($fileName);
            $content = (new PrivateTransformer($content))->transform();

            if ($this->getParameter('generate_getters_setters')) {
                $content = (new GettersSettersTransformer($content, $fileName, $this->getEntityNamespace()))
                    ->transform();
            }

            file_put_contents($fileName, $this->transformClassContent($content));
        }

        $this->doAfterImport();
        $output->writeln('<info>Import complete.</info>');
    }

    /**
     * Here you can overwrite the content of class.
     *
     * @param string $content
     *
     * @return string
     */
    protected function transformClassContent(string $content): string
    {
        return $content;
    }

    /**
     * You can do something after import.
     */
    protected function doAfterImport(): void
    {
    }

    protected function getParameter(string $name)
    {
        return $this->getContainer()->getParameter(Configuration::ROOT_NAME . '.' . $name);
    }

    protected function getBasePath(): string
    {
        return $this->getParameter('base_path');
    }

    protected function getPath(string $path): string
    {
        return "{$this->getBasePath()}/$path";
    }

    protected function getFullPath(string $path): string
    {
        return "{$this->getContainer()->get('kernel')->getRootDir()}/../{$this->getPath($path)}";
    }

    protected function getBaseNamespace(): string
    {
        return $this->getParameter('base_namespace');
    }

    protected function getEntityFolder(): string
    {
        return $this->getParameter('entity_folder');
    }

    protected function getEntityPath(): string
    {
        return $this->getFullPath($this->getEntityFolder());
    }

    protected function getEntityNamespace(): string
    {
        $path = str_replace('/', '\\', $this->getEntityFolder());

        return "{$this->getBaseNamespace()}\\$path";
    }

    protected function isForceUpdate(): bool
    {
        return $this->getParameter('force_update');
    }

    protected function getConnection(): string
    {
        return $this->getParameter('connection');
    }
}
