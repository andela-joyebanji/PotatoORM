<?php


use \Mockery as m;
use Pyjac\ORM\Model;

class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * Instance of DatabaseConnection used in test.
     */
    protected $databaseConnection;

    protected $model;

    protected $sqlStatement;

    public function setUp()
    {
        $databaseConnectionStringFactory =
                        m::mock('Pyjac\ORM\DatabaseConnectionStringFactoryInterface');

        $databaseConnectionStringFactory->shouldReceive('createDatabaseSourceString')
                                             ->with(['DRIVER' => 'sqlite', 'HOSTNAME' => '127.0.0.1', 'USERNAME' => '', 'PASSWORD' => '', 'DBNAME' => 'potatoORM', 'PORT' => '54320'])->once()->andReturn('sqlite::memory:');

        $this->databaseConnection = m::mock('Pyjac\ORM\DatabaseConnection');

        $this->sqlStatement = m::mock('\PDOStatement');

        /*$this->databaseConnection = m::mock('Pyjac\ORM\DatabaseConnection[getInstance,createConnection]',array($databaseConnectionStringFactory));*/
        $this->model = $this->getMockForAbstractClass('Pyjac\ORM\Model', [$this->databaseConnection]);
        //= new DatabaseConnection($databaseConnectionStringFactory);
    }

    public function testGetTableNameReturnsCorrectTableName()
    {
        $this->databaseConnection->shouldReceive('createConnection')->with('sqlite::memory:')->once()->andReturn('');
        $this->model->expects($this->any())
             ->method('getTableName')
             ->will($this->returnValue(strtolower(get_class($this->model).'s')));

        $this->assertEquals($this->model->getTableName(), strtolower(get_class($this->model).'s'));
    }

    public function testGetReturnsAnObjectWhenIdIsFoundInDatabase()
    {
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('setFetchMode');
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('rowCount')->once()->andReturn(1);
        $this->sqlStatement->shouldReceive('fetch')->once()->andReturn(new stdClass());

        $this->assertInstanceOf('stdClass', $this->model->get(1));
    }

    /**
     * @expectedException Pyjac\ORM\Exception\ModelNotFoundException
     */
    public function testGetThrowsModelNotFoundExceptionWhenIdNotFoundInDatabase()
    {
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('setFetchMode');
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('rowCount')->once()->andReturn(0);

        $this->assertInstanceOf('stdClass', $this->model->get(1));
    }

    public function testAllReturnsAnArrayOfObjectsWhenValuesAreInDatabase()
    {
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('fetchAll')->once()->andReturn([new stdClass(), new stdClass()]);
        $this->assertContainsOnlyInstancesOf('stdClass', $this->model->all());
    }

    public function testUpdateChangesTheValueOfObjectInDatabase()
    {
        $this->model->setProperties(['id' => 2, 'name' => 'pyjac', 'age' => '419']);
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('rowCount')->once()->andReturn(1);

        $this->assertEquals(1, $this->model->update());
    }

    public function testCreateObjectInDatabase()
    {
        $this->model->setProperties(['id' => 2, 'name' => 'pyjac', 'age' => '419']);
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('rowCount')->once()->andReturn(1);

        $this->assertEquals(1, $this->model->create());
    }

    public function testSaveShouldCreateNewModelInDatabaseWhenIdNotPresent()
    {
        $this->model->setProperties(['name' => 'pyjac', 'age' => '419']);
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('rowCount')->once()->andReturn(1);
        $this->assertEquals(1, $this->model->save());
    }

    public function testSaveShouldUpdateModelInDatabaseIfIdIsPresent()
    {
        $this->model->setProperties(['id' => 2, 'name' => 'pyjac', 'age' => '419']);
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('rowCount')->once()->andReturn(1);
        $this->assertEquals(1, $this->model->save());
    }

    public function testGetPropertiesReturnsExpectedArrayValues()
    {
        $values = ['id' => 2, 'name' => 'pyjac', 'age' => '419'];
        $this->model->setProperties($values);

        $this->assertEquals($values, $this->model->getProperties());
    }

    public function testDeleteReturnsTrueWhenModelIsSuccessfullyRemovedFromDatabase()
    {
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('rowCount')->once()->andReturn(1);

        $this->assertEquals(true, $this->model->delete(1));
    }

    public function testDeleteReturnsFalseWhenModelWasNotDeletedFromDatabase()
    {
        $this->databaseConnection->shouldReceive('prepare')->once()->andReturn($this->sqlStatement);
        $this->sqlStatement->shouldReceive('execute');
        $this->sqlStatement->shouldReceive('rowCount')->once()->andReturn(0);

        $this->assertEquals(false, $this->model->delete(1));
    }

    public function testMagicMethodsReturnCorrectResult()
    {
        $this->model->id = '10';
        $this->model->name = 'Pyjac';
        $this->model->age = '60';

        $this->assertEquals($this->model->name, 'Pyjac');
        $this->assertEquals($this->model->id, '10');
        $this->assertEquals($this->model->age, '60');
    }
}
