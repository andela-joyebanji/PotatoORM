<?php


namespace Pyjac\ORM;

use Pyjac\ORM\Exception\DatabaseDriverNotSupportedException;

class DatabaseConnectionStringFactory implements DatabaseConnectionStringFactoryInterface
{
    /**
     * Create a connection string.
     *
     * @param array $config
     *
     * @throws Pyjac\PotatoORM\Exception\DatabaseDriverNotSupportedException
     *
     * @return string
     */
    public function createDatabaseSourceString($config)
    {
        $driver = $config['DRIVER'];

        switch ($driver) {
            case 'sqlite':
                $dsn = $driver.'::memory:';
                break;
            case 'mysql':
            case 'postgres':
                if (strcasecmp($driver, 'postgres') == 0) {
                    $driver = 'pgsql';
                }
                $dsn = $driver.':host='.$config['HOSTNAME'].';dbname='.$config['DBNAME'];
                if (isset($config['PORT'])) {
                    $dsn .= ';port='.$config['PORT'];
                }
                break;
            default:
                throw new DatabaseDriverNotSupportedException();
        }

        return $dsn;
    }
}
