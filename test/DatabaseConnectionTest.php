<?php

use \Mockery as m;
use Pyjac\ORM\DatabaseConnection;

class DatabaseConnectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Instance of DatabaseConnection used in test.
     */
    protected $databaseConnection;

    public function setUp()
    {
        $databaseConnectionStringFactory =
                        m::mock('Pyjac\ORM\DatabaseConnectionStringFactoryInterface');
        $databaseConnectionStringFactory->shouldReceive('createDatabaseSourceString')
                                             ->with(['DRIVER' => 'sqlite', 'HOSTNAME' => '127.0.0.1', 'USERNAME' => '', 'PASSWORD' => '', 'DBNAME' => 'potatoORM', 'PORT' => '54320'])->once()->andReturn('sqlite::memory:');

        $this->databaseConnection = new DatabaseConnection($databaseConnectionStringFactory);
    }

    public function testCreateConnectionReturnsDatabaseConnection()
    {
        $dbInstance = $this->databaseConnection->createConnection('sqlite::memory:');

        $this->assertInstanceOf('PDO', $dbInstance);
    }

    public function testGetInstanceReturnsCorrectInstance()
    {
        $dbInstance = DatabaseConnection::getInstance();

        $this->assertInstanceOf('Pyjac\ORM\DatabaseConnection', $dbInstance);
    }

    public function testSetOptionsAndGetOptionsReturnsCorrectValue()
    {
        $options = [
            PDO::ATTR_CASE              => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_EMULATE_PREPARES  => false,
        ];
        $this->databaseConnection->setDefaultOptions($options);

        $this->assertEquals($this->databaseConnection->getDefaultOptions(), $options);
    }

    public function testTryAgainIfCausedByLostConnectionCreateNewConnectionWhenReasonForExceptionIsConnectionLoss()
    {
        $e = new \Exception('Error while sending');
        $result = $this->invokeMethod($this->databaseConnection, 'tryAgainIfCausedByLostConnection', [$e, 'sqlite::memory:', '', '', []]);

        $this->assertInstanceOf('PDO', $result);
    }

    /**
     * @expectedException \Exception
     */
    public function testTryAgainIfCausedByLostConnectionThrowsExceptionWhenReasonForExceptionIsNotConnectionLoss()
    {
        $e = new \Exception('PHP Rocks !!!');
        $result = $this->invokeMethod($this->databaseConnection, 'tryAgainIfCausedByLostConnection', [$e, 'sqlite::memory:', '', '', []]);
    }

    /**
     * Reference: https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
