<?php


namespace Pyjac\ORM\Exception;

class ModelNotFoundException extends \Exception
{
    public function __construct($id)
    {
        parent::__construct('The requested Model with '.$id.' does not exist');
    }
}
