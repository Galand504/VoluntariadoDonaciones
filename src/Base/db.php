<?php
// db.php - Archivo de conexión a la base de datos usando POO

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
            echo "Error en la conexión: " . $e->getMessage();
        }

        return $this->conn;
    }
}

// Instancia la conexión y guárdala en $db
$db = (new Database())->connect();
?>
