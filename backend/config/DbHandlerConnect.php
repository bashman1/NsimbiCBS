<?php
require_once 'config.php';
require_once('../../../vendor/autoload.php');

use Dibi\Connection;

class DbHandlerConnect
{
    public $database;

    public function __construct()
    {
        $database = new Connection([
            'driver'   => 'postgre',
            'host'     => DB_HOST,
            'username' => DB_USER,
            'password' => DB_PASS,
            'database' => DB_NAME,
            'port' => DB_PORT,

        ]);

        $this->database = $database;
    }

    public function connect()
    {
        return $this->database;
    }
}
