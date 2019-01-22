<?php

/*
 * (c) Rafał Mikołajun <root@rmweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\EntityTransformer;

final class GettersSettersTransformer implements EntityTransformerInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $entityNamespace;

    public function __construct(string $code, string $fileName, string $entityNamespace)
    {
        $this->code = $code;
        $this->fileName = $fileName;
        $this->entityNamespace = $entityNamespace;
    }

    public function transform(): string
    {
        $className = $this->entityNamespace . '\\' . pathinfo($this->fileName)['filename'];
        require_once $this->fileName;
        $reflection = new \ReflectionClass($className);

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE)
                 as $property
        ) {
            // TODO WIP
            dump($property->getName());

            preg_match('/@var\s+(.*)/', $property->getDocComment(), $matches);

            if (count($matches) === 2) {
                $types = explode('|', $matches[1]);

                foreach ($types as $k => $val) {
                    $types[$k] = trim($val);
                }

                $typesCount = count($types);
                $nullable = false;

                if ($typesCount === 1) {
                    $type = $types[0];
                } elseif ($typesCount === 2 && in_array('null', $types, true)) {
                    $type = array_diff($types, ['null'])[0];
                    $nullable = true;
                } else {
                    $type = null;
                }

                // TODO WIP
                dump($type);
                dump($nullable);
                dump($types);
                dump('----------------------------');
            }
        }

        return $this->code;
    }
}
