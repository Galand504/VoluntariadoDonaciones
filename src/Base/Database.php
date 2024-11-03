<?php
namespace App\Base;

use PDO;
use PDOException;
use Exception;

class Database {
    private $host = 'localhost';
    private $db_name = 'voluntariadodonaciones';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Error en la conexión: " . $e->getMessage());
        }

        return $this->conn;
    }

    public function disconnect() {
        $this->conn = null;
    }
}

// Instancia la conexión y guárdala en $db
try {
    $db = (new Database())->connect();
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

