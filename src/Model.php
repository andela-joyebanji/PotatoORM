<?php 

namespace Pyjac\ORM;

use PDO;

abstract class Model implements ModelInterface
{

   
    protected  $properties = [];

    /**
     * Store instance of database connection used.
    * @var [type]
    */
    protected  $databaseConnection;

    /**
     *  The id of the model.
     *  
     * @property string $id
    */
     
    /**
     * Create a model instance.
     * 
     */
     public function __construct()
    {
        $this->databaseConnection = DatabaseConnection::getInstance()->databaseConnection;
       
    }
    /**
     * Sets into $properties the $key => $value pairs
     * 
    * @param string $key 
    * @param string $val 
    *
    */
    public  function __set($key, $val)
    {
        $this->properties[$key] = $val;
    }
    /**
    * @param string $key
    * 
    * @return array
    */
    public function __get($key)
    {
        return $this->properties[$key];
    }
    /**
     * Get all the model properties.
     *
     * @return array
     */
     public function getProperties()
     {
         return $this->properties;
     }
    /**
    * Gets the name of the child class with a 's'.
    *
    * @return string
    */
    public function getTableName()
    {
        $className = explode('\\', get_called_class());
        $table = strtolower(end($className) .'s');
        return $table;
    }
    /**
    * Find the particular model with the passed id.
    * 
    * @param int $id
    * 
    * @return object
    */
    public static function find($id)
    {
        $model = new static;
        return $model->get($id); 
    }

    /**
    * Get the particular model with the passed id.
    * 
    * @param int $id
    * 
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

    /**
    * Get all the models from the database.
    * 
    * @return array
    */
    public static function getAll()
    {
        $model = new static;
        return $model->all();
    }

    /**
    * Returns all the models from the database.
    * 
    * @return array
    */
    public function all()
    {
        $sql = "SELECT * FROM {$this->getTableName()}";
        $row = $this->databaseConnection->prepare($sql);
        $row->execute();
       
        return $row->fetchAll(PDO::FETCH_CLASS);

    }
    /** 
     * Update the model in the database.
     * 
     * @return int
    */
    private function update()
    {
       
        $bindNameParameters = [];
        $sqlUpdate = "UPDATE " . $this->getTableName() . " SET " ;
        foreach ($this->properties as $columnName => $columnValue) {
            if($columnName == 'id') continue;
            $bindColumnName = ':' . $columnName;
            $sqlUpdate .= "$columnName = $bindColumnName,";
            $bindNameParameters[$bindColumnName] = $columnValue;
        }
        //Remove the last comma in sql command then join it to the other query part.
        $sqlUpdate = substr($sqlUpdate, 0, -1)." WHERE id = :id";
        $sqlStatement = $this->databaseConnection->prepare($sqlUpdate);
        $sqlStatement->bindValue(":id", $this->properties['id']);
        $sqlStatement->execute($bindNameParameters);
        return $sqlStatement->rowCount();
    }

    /**
    * Insert the model values into the database.
    *
    * @return int
    */
    private function create()
    {
        
        $columnNames = "";
        $columnValues = "";
        $bindNameParameters = [];
        $sqlCreate = "INSERT" . " INTO " . $this->getTableName()." (";
        foreach ($this->properties as $columnName => $columnValue) {

            $bindColumnName = ':' . $columnName;
            $columnNames .= $columnName.",";
            $columnValues .= $bindColumnName.",";
            $bindNameParameters[$bindColumnName] = $columnValue;
        }
        // Remove ending comma and whitespace.
        $columnNames = substr($columnNames, 0, -1);
        $columnValues = substr($columnValues, 0, -1);

        $sqlCreate .= $columnNames.') VALUES (' .$columnValues.')';
        $sqlStatement = $this->databaseConnection->prepare($sqlCreate);
        $sqlStatement->execute($bindNameParameters);
        return $sqlStatement->rowCount();
    }
    
    /**
     * Save the model data to the database.
     * 
     * @return boolean
     */
    public function save()
    {
        return $this->id ? $this->update() : $this->create();
    }

   /**
    * Delete a model from the database. 
    * @param  int $id 
    * @return boolean
    */
    public static function destroy($id)
    {
        $model = new static;
        return $model->delete($id); 
    }

    /**
     * Delete model from the database.
     * 
     * @param  int $id
     * @return boolean
     */
    public function delete($id)
    {
        $sql = "DELETE" . " FROM " . self::getTableName()." WHERE id = ". $id;
        $sqlStatment = $this->databaseConnection->prepare($sql);
        $sqlStatment->execute();
        return ($sqlStatment->rowCount() > 0) ? true : false;
    }

}