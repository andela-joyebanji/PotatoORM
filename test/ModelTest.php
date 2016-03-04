<?php 

use Pyjac\ORM\Model;

class ModelTest extends PHPUnit_Framework_TestCase
{
	protected $model;

	public function setUp(){
		$this->model =  $this->getMockForAbstractClass('Pyjac\ORM\Model');
	}

	public function testGetTableNameReturnsCorrectTableName()
    {
        $this->model->expects($this->any())
             ->method('getTableName')
             ->will($this->returnValue(strtolower(get_class($stub).'s')));

        $this->assertEquals($stub->getTableName(), strtolower(get_class($stub).'s'));
    }

    public function testGetTableNameReturnsCorrectTableName()
    {
        $this->model->expects($this->any())
             ->method('getTableName')
             ->will($this->returnValue(strtolower(get_class($stub).'s')));

        $this->assertEquals($stub->getTableName(), strtolower(get_class($stub).'s'));
    }

}