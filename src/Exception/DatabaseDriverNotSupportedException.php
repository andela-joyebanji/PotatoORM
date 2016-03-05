<?php


namespace Pyjac\ORM\Exception;

class DatabaseDriverNotSupportedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Database driver not supported.');
    }
}
