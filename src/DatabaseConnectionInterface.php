<?php

namespace Pyjac\ORM;

interface DatabaseConnectionInterface
{
    /**
     * Get the instance of the class.
     *
     * @return Pyjac\ORM\DatabaseConnection
     */
    public static function getInstance();

    /**
     * Create a new PDO connection.
     *
     * @param string $dsn
     *
     * @return \PDO
     */
    public function createConnection($dsn);
}
