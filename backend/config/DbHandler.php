<?php
require_once 'config.php';
require_once 'DbHandlerConnect.php';
require_once('../../../vendor/autoload.php');

use Dibi\Connection;

class DbHandler
{
    public $database;

    public function __construct()
    {
        $connection = new DbHandlerConnect();
        $this->database = $connection->connect();
    }

    /**
     * Fetches all records from the database
     * Add field and field value to filter
     */

    public function query($query)
    {
        return $this->database->query($query);
    }

    public function fetchAll($table, $field = null, $value = null)
    {
        if ($field && $value) {
            return $this->database->fetchAll('SELECT * FROM %n WHERE %n = ?', $table, $field, $value);
        }

        if (is_array($field)) {
            return $this->database->fetchAll('SELECT * FROM %n WHERE %and ', $table, $field);
        }
        return $this->database->fetchAll('SELECT * FROM  %n ', $table);
    }

    /**
     * fetches single record from the database
     */
    public function fetch($table, $field, $value = null)
    {

        if ($field && $value) {
            return $this->database->fetch('SELECT * FROM %n WHERE %n = ? ', $table, $field, $value);
        }

        if (is_array($field)) {
            return $this->database->fetch('SELECT * FROM %n WHERE %and  ', $table, $field);
        }
    }

    /**
     * fetches single record from the database
     */
    public function fetchSingle($table, $field, $value)
    {
        return $this->database->fetchSingle('SELECT * FROM %n WHERE %n = ? ', $table, $field, $value);
    }

    public function WhereIn($table, $field, $array)
    {
        return $this->database->fetchSingle('SELECT * FROM %n WHERE %n IN (%i) ', $table, $field, $array);
    }

    /**
     * inserts into the database
     */
    public function insert($table, $array)
    {
        $this->database->query('INSERT INTO %n %v', $table, $array);
        return $this->database->getInsertId();
    }

    /**
     * inserts Insert an entry or update if it already exists:
     */
    public function upsert($table, $array, $update_id = 'id')
    {
        // $id_value = $array[$update_id];
        $first_array = $array;
        $first_array[$update_id] = $array[$update_id];
        unset($array[$update_id]);
        // var_dump($first_array);
        var_dump($array);
        return $this->database->query('INSERT INTO ' . $table, $first_array, 'ON DUPLICATE KEY UPDATE %a', $array);
    }

    /**
     * inserts data as a batch database
     */
    public function insert_multiple($table, $array)
    {
        $this->database->query('INSERT INTO %n %v', $table, $array);
        return $this->database->getInsertId();
    }

    /**
     * update data
     */
    public function update($table, $array, $field, $value)
    {
        // return $this->database->query('UPDATE ' . $table . ' SET', $array, 'WHERE ' . $field . ' = ?', $value);

        return $this->database->query('UPDATE %n SET %a WHERE %n = ?', $table, $array, $field, $value);
    }

    public function update2($table, $array)
    {
        // return $this->database->query('UPDATE ' . $table . ' SET', $array, 'WHERE ' . $field . ' = ?', $value);

        return $this->database->query('UPDATE %n SET %a ', $table, $array);
    }

    /**
     * deletes records from the database
     */
    public function delete($table, $field, $value)
    {
        // return $this->database->query('DELETE FROM ' . $table . ' WHERE ' . $field . ' = ?', $value);

        return $this->database->query('DELETE FROM %n WHERE %n = ?', $table, $field, $value);
    }

    public function beginTransaction()
    {
        return $this->database->beginTransaction();
    }

    public function commit()
    {
        return $this->database->commit();
    }

    public function rollback()
    {
        return $this->database->rollback();
    }
}
