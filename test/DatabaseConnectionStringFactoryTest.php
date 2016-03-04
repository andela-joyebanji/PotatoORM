<?php 

use Pyjac\ORM\DatabaseConnectionStringFactory;
use Pyjac\ORM\Exception\DatabaseDriverNotSupportedException;

class DatabaseConnectionStringFactoryTest extends PHPUnit_Framework_TestCase
{
     /**
     * The instance of DatabaseConnectionStringFactory used in the test.
     *
     * @var Pyjac\ORM\DatabaseConnectionStringFactory
     */
    protected $databaseConnectionStringFactory;

    /**
     * Configuration array.
     * @var array
     */
    protected $config;

    protected function setUp()
    {   
        $this->config = ['DBNAME' => 'Pyjac', 'DRIVER' => '', 'PASSWORD' => 'secret', 'HOSTNAME' => 'localhost', 'USERNAME' => 'pyjac'];
        $this->databaseConnectionStringFactory = new DatabaseConnectionStringFactory();
    }

    public function testCreateDatabaseSourceStringReturnsCorrectPostgresDatabaseSourceString()
    {
        $this->config['DRIVER']= 'postgres';
        $dsn = $this->databaseConnectionStringFactory->createDatabaseSourceString($this->config);
        $this->assertEquals("pgsql:host=localhost;dbname=Pyjac", $dsn);
    }

    public function testCreateDatabaseSourceStringReturnsCorrectMYSqlDatabaseSourceString()
    {
        $this->config['DRIVER']= 'mysql';
        $dsn = $this->databaseConnectionStringFactory->createDatabaseSourceString($this->config);
        $this->assertEquals("mysql:host=localhost;dbname=Pyjac", $dsn);
    }

    public function testCreateDatabaseSourceStringReturnsCorrectSQLiteDatabaseSourceString()
    {
        $this->config['DRIVER']= 'sqlite';
        $dsn = $this->databaseConnectionStringFactory->createDatabaseSourceString($this->config);
        $this->assertEquals("sqlite::memory:", $dsn);
    }

     /**
     * @expectedException Pyjac\ORM\Exception\DatabaseDriverNotSupportedException
     */
    public function testCreateDatabaseSourceStringThrowsDatabaseDriverNotSupportedExceptionWhenUnknownDriverIsPassed()
    {
        $this->config['DRIVER']= 'pyjac';
        $dsn = $this->databaseConnectionStringFactory->createDatabaseSourceString($this->config);
    }
}