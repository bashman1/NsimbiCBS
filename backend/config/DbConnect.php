<?php
include_once 'config.php';
class DbConnect
{
  // DB Params
  private $host = DB_HOST;
  // private $db_name = 'winnejjh_winnerssacco_test';
  // private $username = 'winnejjh_winnejjh_test';
  // private $password = '}8^SPz@!CxY9';

  private $db_name = DB_NAME;
  private $username = DB_USER;
  private $password = DB_PASS;

  private $conn;

  // DB Connect
  public function connect()
  {
    $this->conn = null;

    try {
      $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name. ';sslmode=require ', $this->username, $this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // Disable parallel query execution for the session
      // $this->conn->exec("SET max_parallel_workers_per_gather = 0");
    } catch (PDOException $e) {
      echo 'Connection Error: ' . $e->getMessage();
      exit;
    }

    return $this->conn;
  }
}
