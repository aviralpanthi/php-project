<?php
// includes/Database.php
class Database
{
    private $host = 'localhost';
    private $dbname = 'aviral';
    private $username = 'root';
    private $password = 'Console@123';
    public $connection;


    public function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}
?>