<?php

namespace Pyjac\ORM;

use Doctrine\Common\Inflector\Inflector;
use PDO;
use Pyjac\ORM\Exception\ModelNotFoundException;

abstract class Model implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    protected $properties = [];

    /**
     * Store instance of database connection used.
     *
     * @var Pyjac\ORM\DatabaseConnection
     */
    protected $databaseConnection;

     /**
      *  The id of the model.
      *
      * @property string $id
      */

     /**
      * Create a model instance.
      */
     public function __construct(DatabaseConnectionInterface $databaseConnection = null)
     {
         if ($databaseConnection == null) {
             $this->databaseConnection = DatabaseConnection::getInstance()->databaseConnection;
         } else {
             $this->databaseConnection = $databaseConnection;
         }
     }

    /**
     * Sets into $properties the $key => $value pairs.
     *
     * @param string $key
     * @param string $val
     */
    public function __set($key, $val)
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
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }
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
      * Set model properties.
      */
     public function setProperties(array $properties)
     {
         $this->properties = $properties;
     }

    /**
     * Pluralize the name of the child class.
     *
     * @return string
     */
    public function getTableName()
    {
        if (isset($this->table) && !empty($this->table)) {
            return $this->table;
        }
        $className = explode('\\', get_called_class());

        return Inflector::pluralize(strtolower(end($className)));
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
        $model = new static();

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
        if ($sqlStatement->rowCount() < 1) {
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
        $model = new static();

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
        $sqlStatement = $this->databaseConnection->prepare($sql);
        $sqlStatement->execute();

        return $sqlStatement->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * Update the model in the database.
     *
     * @return int
     */
    public function update()
    {
        $bindNameParameters = [];
        $sqlUpdate = 'UPDATE '.$this->getTableName().' SET ';
        foreach ($this->properties as $columnName => $columnValue) {
            if ($columnName == 'id') {
                continue;
            }
            $bindColumnName = ':'.$columnName;
            $sqlUpdate .= "$columnName = $bindColumnName,";
            $bindNameParameters[$bindColumnName] = $columnValue;
        }
        //Remove the last comma in sql command then join it to the other query part.
        $sqlUpdate = substr($sqlUpdate, 0, -1).' WHERE id = :id';
        $sqlStatement = $this->databaseConnection->prepare($sqlUpdate);
        $bindNameParameters[':id'] = $this->properties['id'];
        $sqlStatement->execute($bindNameParameters);

        return $sqlStatement->rowCount();
    }

    /**
     * Insert the model values into the database.
     *
     * @return int
     */
    public function create()
    {
        $columnNames = '';
        $columnValues = '';
        $bindNameParameters = [];
        $sqlCreate = 'INSERT'.' INTO '.$this->getTableName().' (';
        foreach ($this->properties as $columnName => $columnValue) {
            $bindColumnName = ':'.$columnName;
            $columnNames .= $columnName.',';
            $columnValues .= $bindColumnName.',';
            $bindNameParameters[$bindColumnName] = $columnValue;
        }
        // Remove ending comma and whitespace.
        $columnNames = substr($columnNames, 0, -1);
        $columnValues = substr($columnValues, 0, -1);

        $sqlCreate .= $columnNames.') VALUES ('.$columnValues.')';
        $sqlStatement = $this->databaseConnection->prepare($sqlCreate);
        $sqlStatement->execute($bindNameParameters);

        return $sqlStatement->rowCount();
    }

    /**
     * Save the model data to the database.
     *
     * @return bool
     */
    public function save()
    {
        return isset($this->properties['id']) ? $this->update() : $this->create();
    }

    /**
     * Delete a model from the database.
     *
     * @param int $id
     *
     * @return bool
     */
    public static function destroy($id)
    {
        $model = new static();

        return $model->delete($id);
    }

    /**
     * Delete model from the database.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete($id)
    {
        $sql = 'DELETE'.' FROM '.self::getTableName().' WHERE id = '.$id;
        $sqlStatment = $this->databaseConnection->prepare($sql);
        $sqlStatment->execute();

        return ($sqlStatment->rowCount() > 0) ? true : false;
    }
}
