<?php 

namespace Pyjac\ORM;

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
        $sqlStatement->setFetchMode($this->databaseConnection::FETCH_CLASS, get_called_class());
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

}