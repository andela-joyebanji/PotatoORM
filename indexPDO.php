<?php


interface ModelInterface {
	/**
	 * Get all models from database.
	 *
	 * @return array
	 */
	function static getAll();

	/**
	 * Find model with the specified id.
	 */
	function static find($id);

	/**
	 * Delete model with the specified id.
	 * 
	 */
	function static destroy($id);
}

abstract Model {

	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection;


}

class DatabaseConnectionFactory {




}


class Connection {

	/**
     * The current globally used instance.
     *
     * @var object
     */
    protected static $instance;

    private $host;
    private $user;
    private $pass;
    private $dbname;
 
    private $dbh;
    private $error;
 
    public function __construct(){
    	$config = parse_ini_file('config.ini');
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instanace
        try{
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        // Catch any errors
        catch(PDOException $e){
            $this->error = $e->getMessage();
        }
    }
}


try {
	 
	$dbh = new PDO('mysql:host=localhost;dbname=potatoORM', 'homestead', 'secret');
	foreach($dbh->query('SELECT * from test') as $row) {
	        print_r($row);
	    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$stmt = $dbh->prepare("INSERT INTO test (id, body) VALUES (?, ?)");
$stmt->bindParam(1, $id);
$stmt->bindParam(2, $body);

// insert one row
$id = 3;
$body = 'Some other test msg';
$stmt->execute();

// insert another row with different values
$id = 4;
$body = 'Some other test msg 2';
$stmt->execute();
$dbh = null;

class Model {



	function __construct()
	{
		/**
        * Load the environment variables
        * @return connection object
        */
        $this->loadDotenv();

        $this->database = getenv('DB_DATABASE');
        $this->host = getenv('DB_host');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
        $this->driver = getenv('DB_CONNECTION');

	}
	/**
    * use vlucas dotenv to access the .env file
    **/
    protected function loadDotenv()
    {
        if(getenv('APP_ENV') !== 'production')
        {
            $dotenv = new Dotenv(__DIR__);
            $dotenv->load();
        }
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;

        return call_user_func_array([$instance, $method], $parameters);
    }	

}


class MySqlConnector extends DatabaseAdapter implements DatabaseAdapterInterface
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config)
    {
        $dsn = $this->getDsn($config);

        $options = $this->getOptions($config);

        // We need to grab the PDO options that should be used while making the brand
        // new connection instance. The PDO options control various aspects of the
        // connection's behavior, and some might be specified by the developers.
        $connection = $this->createConnection($dsn, $config, $options);

        $collation = $config['collation'];

        // Next we will set the "names" and "collation" on the clients connections so
        // a correct character set will be used by this client. The collation also
        // is set on the server but needs to be set here on this client objects.
        $charset = $config['charset'];

        $names = "set names '$charset'".
            (! is_null($collation) ? " collate '$collation'" : '');

        $connection->prepare($names)->execute();

        return $connection;
    }

    /**
     * Create a DSN string from a configuration.
     *
     * Chooses socket or host/port based on the 'unix_socket' config value.
     *
     * @param  array   $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        return $this->configHasSocket($config) ? $this->getSocketDsn($config) : $this->getHostDsn($config);
    }

    /**
     * Determine if the given configuration array has a UNIX socket value.
     *
     * @param  array  $config
     * @return bool
     */
    protected function configHasSocket(array $config)
    {
        return isset($config['unix_socket']) && ! empty($config['unix_socket']);
    }

    /**
     * Get the DSN string for a socket configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getSocketDsn(array $config)
    {
        return "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}";
    }

    /**
     * Get the DSN string for a host / port configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getHostDsn(array $config)
    {
        extract($config, EXTR_SKIP);

        return isset($port)
                        ? "mysql:host={$host};port={$port};dbname={$database}"
                        : "mysql:host={$host};dbname={$database}";
    }
}


interface DatabaseAdapterInterface
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config);
}


class DatabaseConnection
{
    /**
     * [$instance description]
     * @var [type]
     */
    private static $instance;

    /**
     * [$databaseConnection description]
     * @var [type]
     */
    private $databaseConnection;

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
     * Singleton Class:
     * Constructor reads database config file and  creates connection.
     */
    private function __construct(DatabaseConnectionStringFactoryInterface $dbConnStringFactory)
    {
        //Read config file
        $this->config = parse_ini_file('config.ini');
        $dsn = $dbConnStringFactory->createDatabaseSourceString($this->config);
        $this->createConnection($dsn, $this->options);
    }
   
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
            $pdo = new PDO($dsn, $username, $password, $options);
        } catch (Exception $e) {
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


class Helpers {

	/**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}