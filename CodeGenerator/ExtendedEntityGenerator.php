<?php

/*
 * (c) Rafał Mikołajun <root@rmweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\CodeGenerator;

class ExtendedEntityGenerator
{
    /**
     * @var string
     */
    protected $entityFileName;

    /**
     * @var string
     */
    protected $entityFolder;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $entityNamespace;

    /**
     * @var string
     */
    protected $baseNamespace;
    
    public function __construct(
        string $entityFileName, 
        string $entityFolder, 
        string $basePath, 
        string $entityNamespace, 
        string $baseNamespace
    ) 
    {
        $this->entityFileName = $entityFileName;
        $this->entityFolder = $entityFolder;
        $this->basePath = $basePath;
        $this->entityNamespace = $entityNamespace;
        $this->baseNamespace = $baseNamespace;
    }

    public function generate(): void
    {
        $entityName = pathinfo($this->entityFileName)['filename'];
        $entityDir = "{$this->basePath}{$this->entityFolder}";
        $entityPath = "$entityDir/{$entityName}.php";

        if (!file_exists($entityPath)) {
            if (!file_exists($entityDir)) {
                mkdir($entityDir);
            }

            file_put_contents(
                $entityPath,
                str_replace([
                    '{{entityNamespace}}',
                    '{{baseImport}}',
                    '{{entityName}}',
                ], [
                    $this->entityNamespace,
                    "{$this->baseNamespace}\\$entityName",
                    $entityName,
                ], file_get_contents(__DIR__ . '/../Resources/templates/extended-entity.txt'))
            );
        }
    }
}
