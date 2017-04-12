# Database First Helper Bundle

## Instalation

Requires composer, install as follows:

    composer require mikoweb/db-first-helper-bundle

## Usage

Create command class inside your bundle, eg.

```php
namespace App\DatabaseBundle\Command;

use Mikoweb\Bundle\DbFirstHelperBundle\Command\ImportDatabaseCommandAbstract;

class ImportDatabaseCommand extends ImportDatabaseCommandAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:database:import')
            ->setDescription('Import database schema.')
        ;
    }
}
```

If you want to lock doctrine:schema:update, add follows service:

```yml
services:
    app_database.ignore_tables_listener:
        class: Mikoweb\Bundle\DbFirstHelperBundle\EventListener\IgnoreTablesListener
        tags:
            - {name: doctrine.event_listener, event: postGenerateSchema }

```

## Configuration

`config.yml`

```yml
mikoweb_db_first_helper:
    bundle_directory: src/App/DatabaseBundle
    bundle_name:      AppDatabaseBundle
    bundle_namespace: App\DatabaseBundle
```
