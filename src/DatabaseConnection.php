<?php

namespace Pyjac\ORM;

use PDO;
use Pyjac\ORM\DatabaseConnectionStringFactoryInterface;
use Pyjac\ORM\DatabaseConnectionStringFactory;

class DatabaseConnection
{
    /**
     * The instance of this class.
     * 
     * @var Pyjac\ORM\DatabaseConnection.
     */
    private static $instance;

    /**
     * The PDO database connection in use.
     * 
     * @var \PDO
     */
    public $databaseConnection;

    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Create a new Database Connection.
     * 
     * @param DatabaseConnectionStringFactoryInterface $dbConnStringFactory
     */
    private function __construct(DatabaseConnectionStringFactoryInterface $dbConnStringFactory)
    {
        //Read config file
        $this->config = parse_ini_file('config.ini');
        $dsn = $dbConnStringFactory->createDatabaseSourceString($this->config);
        $this->databaseConnection = $this->createConnection($dsn, $this->options);
    }

    /**
     * Get the instance of the class.
     *     
     * @return Pyjac\ORM\DatabaseConnection
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self(new DatabaseConnectionStringFactory);
        }
        return self::$instance;
    }
    

    /**
     * Create a new PDO connection.
     *
     * @param  string  $dsn
     * @param  array   $config
     * @return \PDO
     */
    public function createConnection($dsn, array $config)
    {
        $username = $this->config['USERNAME'];

        $password = $this->config['PASSWORD'];
        

        try {
            $pdo = new PDO($dsn, $username, $password, $this->options);
        } catch (\Exception $e) {
            $pdo = $this->tryAgainIfCausedByLostConnection(
                $e, $dsn, $username, $password, $options
            );
        }

        return $pdo;
    }

    /**
     * Get the default PDO connection options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->options;
    }

    /**
     * Set the default PDO connection options.
     *
     * @param  array  $options
     * @return void
     */
    public function setDefaultOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Handle a exception that occurred during connect execution.
     *
     * @param  \Exception  $e
     * @param  string  $dsn
     * @param  string  $username
     * @param  string  $password
     * @param  array   $options
     * @return \PDO
     *
     * @throws \Exception
     */
    protected function tryAgainIfCausedByLostConnection(Exception $e, $dsn, $username, $password, $options)
    {
        if ($this->causedByLostConnection($e)) {
            return new PDO($dsn, $username, $password, $options);
        }

        throw $e;
    }

    /**
     * Determine if the given exception was caused by a lost connection.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function causedByLostConnection(Exception $e)
    {
        $message = $e->getMessage();

        return Helpers::contains($message, [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'SSL connection has been closed unexpectedly',
            'Deadlock found when trying to get lock',
        ]);
    }
}
