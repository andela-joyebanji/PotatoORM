<?php

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
     * Singleton Class:
     * Constructor reads database config file and  creates connection.
     */
    private function __construct(DatabaseConnectionStringFactoryInterface $dbConnStringFactory)
    {
        //Read config file
        $this->config = parse_ini_file('config.ini');
        $dsn = $dbConnStringFactory->createDatabaseSourceString($this->config);
        $this->databaseConnection = $this->createConnection($dsn, $this->options);
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
            $pdo = new PDO($dsn, $username, $password, $this->options);
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

abstract class Model implements ModelInterface
{

   
    protected  $properties = [];

    /**
     * Store instance of database connection used.
    * @var [type]
    */
    protected  $databaseConnection;

     public function __construct()
    {
        $this->databaseConnection = DatabaseConnection::getInstance()->databaseConnection;
        //$databaseConnection->databaseConnection->connect();
    }
    /**
    * @param string $key rep column name
    * @param string $val rep column value
    * sets into $propertie the $key => $value pairs
    */
    public  function __set($key, $val)
    {
        $this->properties[$key] = $val;
    }
    /**
    * @param string $key reps the column name
    * @return $key and $value
    */
    public function __get($key)
    {
        return $this->properties[$key];
    }
    /**
     * Get all the model properties
     *
     * @return array
     */
     public function getProperties()
     {
         return $this->properties;
     }
    /**
    * Gets the name of the child class only
    * without the namespace
    * @var $className
    * @var $table
    * @return $table
    */
    public function getTableName()
    {
        $className = explode('\\', get_called_class());
        $table = strtolower(end($className) .'s');
        return $table;
    }
    /**
    * returns a particular record
    * @param $id reps the record id
    * @param $connection initialised to null
    * @return object
    */
    public static function find($id)
    {
        $model = new static;
        return $model->get($id); 
    }
    /**
    * returns a particular record
    * @param $id reps the record id
    * @param $connection initialised to null
    * @return object
    */
    public function get($id)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE id={$id}";
        $sqlStatement = $this->databaseConnection->prepare($sql);
        $sqlStatement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $sqlStatement->execute();
        if($sqlStatement->rowCount() < 1){
            throw new ModelNotFoundException($id);
        }
        return $sqlStatement->fetch();
    }
    
    public static function getAll()
    {
        $model = new static;
        return $model->all();
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->getTableName()}";
        $row = $this->databaseConnection->prepare($sql);
        $row->execute();
       
        return $row->fetchAll($this->databaseConnection::FETCH_CLASS);

    }
    /** update table with instance properties
    *
    */
    private function update()
    {
        $connection = $this->getConnection();
        $columnNames = "";
        $columnValues = "";
        $count = 0;
        $update = "UPDATE " . $this->getTableName() . " SET " ;
        foreach ($this->properties as $key => $val) {
            $count++;
            if(($key == 'id')) continue;
            $update .= "$key = '$val'";
            if ($count < count($this->properties) )
            {
                $update .=",";
            }
        }
        $update .= " WHERE id = " . $this->properties['id'];
        $stmt = $connection->prepare($update);
            foreach ($this->properties as $key => $val) {
                if($key == 'id') continue;
            }
        $stmt->execute();
        return $stmt->rowCount();
    }
    /**
    * insert instance data into the table
    */
    private function create()
    {
        $connection = $this->getConnection();
        $columnNames = "";
        $columnValues = "";
        $count = 0;
        $create = "INSERT" . " INTO " . $this->getTableName()." (";
            foreach ($this->properties as $key => $val) {
                $columnNames .= $key;
                $columnValues .= ':' . $key;
                $count++;
                if ($count < count($this->properties))
                {
                    $columnNames .= ', ';
                    $columnValues .= ', ';
                }
            }
        $create .= $columnNames.') VALUES (' .$columnValues.')';
        $stmt = $connection->prepare($create);
            foreach ($this->properties as $key => $val) {
                $stmt->bindValue(':'.$key, $val);
            }
            try {
                // if prop returned and props from db differ throw exception
                $stmt->execute();
            } catch(PDOException $e){
                return $e->getExceptionMessage();
            }
        return $stmt->rowCount();
    }
    /**
    * get db connection
    */
    public function getConnection($connection = null)
    {
        if(is_null($connection))
        {
            return new Connection();
        }
    }
    /**
    * checks if the id exists
    * update if exist
    * create if not exist
    */
    public function save()
    {
        if ($this->id) {
            $this->update();
        } else {
            $this->create();
        }
    }
    /**
    * @param row reps record id
    * @param $connection initialised to null
    * @return boolean
    */
    public static function destroy($id)
    {
        
        $sql = "DELETE" . " FROM " . self::getTableName()." WHERE id = ". $id;
        $delete = $connection->prepare($sql);
        $delete->execute();
        $count = $delete->rowCount();
        if ($count < 1) {
            throw new RecordNotFoundException('Record with id ' . $id . ' does not exist.');
        }
        return ($count > 0) ? true : false;
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
        exit("HERE");
        $instance = new static;

        return call_user_func_array([$instance, $method], $parameters);
    }   
}

interface ModelInterface {
    /**
     * Get all models from database.
     *
     * @return array
     */
    static function getAll();

    /**
     * Find model with the specified id.
     */
    static function find($id);

    /**
     * Delete model with the specified id.
     * 
     */
    static function destroy($id);
}

class DatabaseConnectionStringFactory implements DatabaseConnectionStringFactoryInterface
{
    /**
     * Create a connection string 
     * 
     * @param  array $config
     * @throws Pyjac\PotatoORM\Exception\DatabaseDriverNotSupportedException
     * @return string 
     */
    public function createDatabaseSourceString($config)
    {

        $driver = $config['DRIVER'];

        switch ($driver) {
            case 'sqlite':
                $dsn = $driver.'::memory:';
                break;
            case 'mysql':
            case 'postgres':
                if(strcasecmp($driver, 'postgres') == 0) $driver="pgsql";
                $dsn = $driver.':host='.$config['HOSTNAME'].';dbname='.$config['DBNAME'];
                if(isset($config['PORT'])) $dsn .= ';port='.$config['PORT'];
                break;
            default:
                throw new DatabaseDriverNotSupportedException;
        }
        return $dsn;
    }
}

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



class DatabaseDriverNotSupportedException extends Exception 
{

    function __construct()
    {
        parent::__construct("Database driver not supported.");
    }
}

class ModelNotFoundException extends Exception 
{

    function __construct($id)
    {
        parent::__construct('The requested Model with ' . $id . ' does not exist');
    }
}

class Helpers 
{

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

class User extends Model {

}

$users = User::getAll();
var_dump($users);
var_dump(User::find(1));