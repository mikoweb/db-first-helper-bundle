<?php

/*
 * (c) Rafał Mikołajun <rafal@mikoweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mikoweb\Bundle\DbFirstHelperBundle\EventListener;

use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

/**
 * Ignore tables for schema:update command.
 *
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package mikoweb/db-first-helper-bundle
 */
class IgnoreTablesListener
{
    /**
     * @param GenerateSchemaEventArgs $args
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $args)
    {
        $schema = $args->getSchema();

        foreach ($schema->getTableNames() as $tableName) {
            $schema->dropTable($tableName);
        }

        foreach ($schema->getSequences() as $name => $sequence) {
            $schema->dropSequence($name);
        }
    }
}
