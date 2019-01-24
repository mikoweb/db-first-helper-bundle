<?php

/*
 * (c) Rafał Mikołajun <root@rmweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\Command;

use Mikoweb\Bundle\DbFirstHelperBundle\CodeGenerator\ExtendedEntityGenerator;
use Mikoweb\Bundle\DbFirstHelperBundle\CodeGenerator\RepositoryGenerator;
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
        $entityPath = $this->getEntityPath();
        $entityNamespace = $this->getEntityNamespace();

        if ($this->isExtendedEntities()) {
            if ($entityPath[strlen($entityPath) -1] === '/') {
                $entityPath = substr($entityPath, 0, -1);
            }

            $entityPath .= 'Base';
            $entityNamespace .= 'Base';
        }

        if ($this->isForceUpdate()) {
            $fs = new Filesystem();
            $fs->remove($entityPath);
        }

        $import = $this->getApplication()->find('doctrine:mapping:import');

        $returnCode = $import->run(new ArrayInput([
            'command' => 'doctrine:mapping:import',
            'name' => $entityNamespace,
            'mapping-type' => 'annotation',
            '--path' => $entityPath,
            '--force' => $this->isForceUpdate(),
            '--em' => $this->getConnection(),
        ]), $output);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Import fail.');
        }

        foreach (glob($entityPath . '/*.php') as $fileName) {
            $content = file_get_contents($fileName);
            $content = (new PrivateTransformer($content))->transform();

            if ($this->isGenerateGettersSetters()) {
                $content = (new GettersSettersTransformer($content, $fileName, $entityNamespace))
                    ->transform();
            }

            file_put_contents($fileName, $this->transformClassContent($content));

            if ($this->isMakeRepositories()) {
                $this->makeRepository($fileName);
            }

            if ($this->isExtendedEntities()) {
                $this->makeExtendedEntity($fileName, $entityNamespace);
            }
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

    protected function isGenerateGettersSetters(): bool
    {
        return $this->getParameter('generate_getters_setters');
    }

    protected function isMakeRepositories(): bool
    {
        return $this->getParameter('make_repositories');
    }

    protected function isExtendedEntities(): bool
    {
        return $this->getParameter('extended_entities');
    }

    protected function makeRepository(string $entityFileName): void
    {
        (new RepositoryGenerator($entityFileName, $this->getEntityFolder(), 
            $this->getFullPath(''), $this->getEntityNamespace()))
            ->generate();
    }

    protected function makeExtendedEntity(string $entityFileName, string $baseNamespace): void
    {
        (new ExtendedEntityGenerator($entityFileName, $this->getEntityFolder(),
            $this->getFullPath(''), $this->getEntityNamespace(), $baseNamespace))
            ->generate();
    }
}
