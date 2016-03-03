<?php

namespace Pyjac\ORM;

interface DatabaseConnectionStringFactoryInterface 
{
    /**
     * Create a connection string 
     * 
     * @param  array $config
     * @throws Pyjac\PotatoORM\Exception\DatabaseDriverNotSupportedException
     * @return string 
     */
    public function createDatabaseSourceString($config);
}