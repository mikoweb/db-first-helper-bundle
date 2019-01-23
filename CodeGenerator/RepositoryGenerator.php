<?php

namespace Mikoweb\Bundle\DbFirstHelperBundle\CodeGenerator;

class RepositoryGenerator
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

    public function __construct(string $entityFileName, string $entityFolder, string $basePath, string $entityNamespace)
    {
        $this->entityFileName = $entityFileName;
        $this->entityFolder = $entityFolder;
        $this->basePath = $basePath;
        $this->entityNamespace = $entityNamespace;
    }

    public function generate(): void 
    {
        $entityName = pathinfo($this->entityFileName)['filename'];
        $entityPath = "{$this->entityFolder}/{$entityName}";

        if (strpos($entityPath, 'Entity/') === 0) {
            $repositoryPath = preg_replace('/Entity\//', 'Repository/', $entityPath, 1);
        } else {
            $repositoryPath = "Repository/$entityPath";
        }

        $repositoryPath .= 'Repository';
        $repositoryFileName = "{$this->basePath}{$repositoryPath}.php";
        
        if (!file_exists($repositoryFileName)) {
            $repositoryNamespace = 'App';
            $repositoryClass = pathinfo($repositoryFileName)['filename'];

            foreach (explode('/', $repositoryPath, -1) as $name) {
                $repositoryNamespace .= "\\$name";
            }

            $entityImport = "{$this->entityNamespace}\\$entityName";
            $repositoryDirectory = pathinfo($repositoryFileName)['dirname'];

            if (!file_exists($repositoryDirectory)) {
                mkdir($repositoryDirectory);
            }

            file_put_contents(
                $repositoryFileName,
                str_replace([
                    '{{repositoryNamespace}}',
                    '{{repositoryClass}}',
                    '{{entityName}}',
                    '{{entityImport}}',
                ], [
                    $repositoryNamespace,
                    $repositoryClass,
                    $entityName,
                    $entityImport,
                ], file_get_contents(__DIR__ . '/../Resources/templates/repository.txt'))
            );
        }
    }
}
