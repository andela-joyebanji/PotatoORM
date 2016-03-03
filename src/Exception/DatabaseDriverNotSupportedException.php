<?php 

namespace Pyjac\ORM\Exception;

class DatabaseDriverNotSupportedException extends Exception 
{

    function __construct()
    {
        parent::__construct("Database driver not supported.");
    }
}