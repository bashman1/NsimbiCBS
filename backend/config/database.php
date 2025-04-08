<?php
include_once 'config.php';
class Database
{
  /**
   * Connection
   * @var type 
   */
  private $conn;

  /**
   * Connect to the database and return an instance of \PDO object
   * @return \PDO
   * @throws \Exception
   */

  // DB Params
  private $host = DB_HOST;
  private $db_name = DB_NAME;
  private $username = DB_USER;
  private $password = DB_PASS;
  private $db_url = DB_URL;
  private $port = DB_PORT;

  public function connect()
  {
    $this->conn = null;
    try {
      $this->conn = new PDO('pgsql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';sslmode=require ', $this->username, $this->password);

      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // Disable parallel query execution for the session
      // $this->conn->exec("SET max_parallel_workers_per_gather = 0");
    } catch (PDOException $e) {
      echo json_encode(['error' => 'Connection Error: ' . $e->getMessage()]);
      exit;
    }

    return $this->conn;
  }
}
