<?php 

use Pyjac\ORM\DatabaseConnectionStringFactory;

class DatabaseConnectionStringFactoryTest extends PHPUnit_Framework_TestCase
{
     /**
     * The instance of DatabaseConnectionStringFactory used in the test.
     *
     * @var Pyjac\ORM\DatabaseConnectionStringFactory
     */
    protected $openSourceEvangelistFactory;

    /**
     * Configuration array.
     * @var array
     */
    protected $config;

    protected function setUp()
    {   
        $this->databaseConnectionStringFactoryTest = new DatabaseConnectionStringFactory();
    }

    public function testCreateDatabaseSourceStringReturnsCorrectPostgresDatabaseSourceString()
    {
        $config = ['DBNAME' => 'Pyjac', 'DRIVER' => 'postgres', 'PASSWORD' => 'secret', 'HOSTNAME' => 'localhost', 'USERNAME' => 'pyjac'];
        $dsn = $this->databaseConnectionStringFactoryTest->createDatabaseSourceString($config);
        $this->assertEquals("pgsql:host=localhost;dbname=Pyjac", $dsn);
    }
}