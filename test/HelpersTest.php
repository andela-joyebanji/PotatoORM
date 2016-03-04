<?php 

use Pyjac\ORM\Helpers;

class HelpersTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test array.
     * @var array
     */
    protected $testArray;

    protected function setUp()
    {   
        $this->testArray = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'SSL connection has been closed unexpectedly',
            'Deadlock found when trying to get lock',
        ];
    }
    public function testContainsReturnsFalseWhenStringNotFoundInArray()
     {
        $this->assertFalse(Helpers::contains("PHP rocks", $this->testArray));

     }

    public function testContainsReturnsTrueWhenStringIsFoundInArray()
     {
        $this->assertTrue(Helpers::contains("Error while sending", $this->testArray));

     }
}