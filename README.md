# Database First Helper Bundle

## Instalation

Requires composer, install as follows:

    composer req mikoweb/db-first-helper-bundle

## Usage

Create command class inside your bundle, eg.

```php
namespace App\Command;

use Mikoweb\Bundle\DbFirstHelperBundle\Command\AbstractImportDatabaseCommand;

class DatabaseImportCommand extends AbstractImportDatabaseCommand
{
    protected static $defaultName = 'app:database:import';
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
