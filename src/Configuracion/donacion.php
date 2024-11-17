<?php

namespace App\Configuracion;
use PDO;
use PDOStatement;
use Exception;

class donacion {
    private $db;
    private $table = "donacion";

    public $idDonacion;
    public $monto;
    public $fecha;
    public $id_usuario;
    public $idProyecto;

    // Constructor con la conexión a la base de datos
    public function __construct($db) {
        // Verifica si $db es un objeto PDO válido
        if (!$db instanceof PDO) {
            throw new Exception("La conexión a la base de datos no es válida.");
        }
        $this->db = $db;
    }
    
    // Método para crear una nueva donación con validaciones
    public function createDonacion(): bool {
        // Verifica si la conexión a la base de datos es válida antes de continuar
        if (!$this->db instanceof PDO) {
            throw new Exception("La conexión a la base de datos no es válida.");
        }
// Agregar encabezados CORS
header('Access-Control-Allow-Origin: *'); // Permite solicitudes de cualquier origen
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Métodos permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Encabezados permitidos

// Si es una solicitud OPTIONS (preflight), finaliza aquí
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

        $query = "INSERT INTO " . $this->table . " (monto, fecha, id_usuario, idProyecto) VALUES (:monto, :fecha, :id_usuario, :idProyecto)";
        $stmt = $this->db->prepare($query);

        // Limpiar los datos
        $this->sanitize();

        // Asignar los valores usando sentencias preparadas para mayor seguridad
        $stmt->bindParam(':monto', $this->monto);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':idProyecto', $this->idProyecto, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Validar los campos
    private function validateFields(): bool {
        if (empty($this->monto) || !is_numeric($this->monto) || $this->monto <= 0) {
            echo "Error: Monto inválido.<br>";
            return false;
        }

        if (empty($this->fecha) || !strtotime($this->fecha)) {
            echo "Error: Fecha inválida.<br>";
            return false;
        }

        if (empty($this->id_usuario) || !is_numeric($this->id_usuario) || $this->id_usuario <= 0) {
            echo "Error: ID de usuario inválido.<br>";
            return false;
        }

        if (empty($this->idProyecto) || !is_numeric($this->idProyecto) || $this->idProyecto <= 0) {
            echo "Error: ID de proyecto inválido.<br>";
            return false;
        }

        return true;
    }

    // Limpiar datos para evitar ataques
    private function sanitize() {
        $this->monto = htmlspecialchars(strip_tags($this->monto));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->idProyecto = htmlspecialchars(strip_tags($this->idProyecto));
    }

    // Obtener todas las donaciones
    public function obtenerDonaciones(): PDOStatement {
        $query = "SELECT * FROM " . $this->table . " ORDER BY fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener una donación específica
    public function obtenerDonacion(): bool {
        $query = "SELECT * FROM " . $this->table . " WHERE idDonacion = :idDonacion";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idDonacion', $this->idDonacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->monto = $row['monto'];
            $this->fecha = $row['fecha'];
            $this->id_usuario = $row['id_usuario'];
            $this->idProyecto = $row['idProyecto'];
            return true;
        }
        return false;
    }

    // Actualizar una donación
    public function actualizarDonacion(): bool {
        if (!$this->validateFields()) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET monto = :monto, fecha = :fecha, id_usuario = :id_usuario, idProyecto = :idProyecto WHERE idDonacion = :idDonacion";
        $stmt = $this->db->prepare($query);

        $this->sanitize();
        
        $stmt->bindParam(':monto', $this->monto);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':idProyecto', $this->idProyecto, PDO::PARAM_INT);
        $stmt->bindParam(':idDonacion', $this->idDonacion, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Eliminar una donación
    public function eliminarDonacion(): bool {
        $query = "DELETE FROM " . $this->table . " WHERE idDonacion = :idDonacion";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idDonacion', $this->idDonacion, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
