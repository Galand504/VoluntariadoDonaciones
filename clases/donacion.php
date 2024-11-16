<?php
class Donacion {
    private $conn;
    private $table = "donacion";

    public $idDonacion;
    public $monto;
    public $fecha;
    public $idUsuario;
    public $idProyecto;

    // Constructor con la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear una nueva donación con validaciones
    public function createDonacion(): bool {
        // Validaciones de datos
        if (!$this->validateFields()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . " (monto, fecha, idUsuario, idProyecto) VALUES (:monto, :fecha, :idUsuario, :idProyecto)";
        $stmt = $this->conn->prepare($query);

        // Limpiar los datos
        $this->sanitize();

        // Asignar los valores usando sentencias preparadas para mayor seguridad
        $stmt->bindParam(':monto', $this->monto);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
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

        if (empty($this->idUsuario) || !is_numeric($this->idUsuario) || $this->idUsuario <= 0) {
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
        $this->idUsuario = htmlspecialchars(strip_tags($this->idUsuario));
        $this->idProyecto = htmlspecialchars(strip_tags($this->idProyecto));
    }

    // Obtener todas las donaciones
    public function obtenerDonaciones(): PDOStatement {
        $query = "SELECT * FROM " . $this->table . " ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener una donación específica
    public function obtenerDonacion(): bool {
        $query = "SELECT * FROM " . $this->table . " WHERE idDonacion = :idDonacion";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idDonacion', $this->idDonacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->monto = $row['monto'];
            $this->fecha = $row['fecha'];
            $this->idUsuario = $row['idUsuario'];
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

        $query = "UPDATE " . $this->table . " SET monto = :monto, fecha = :fecha, idUsuario = :idUsuario, idProyecto = :idProyecto WHERE idDonacion = :idDonacion";
        $stmt = $this->conn->prepare($query);

        $this->sanitize();
        
        $stmt->bindParam(':monto', $this->monto);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idProyecto', $this->idProyecto, PDO::PARAM_INT);
        $stmt->bindParam(':idDonacion', $this->idDonacion, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Eliminar una donación
    public function eliminarDonacion(): bool {
        $query = "DELETE FROM " . $this->table . " WHERE idDonacion = :idDonacion";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idDonacion', $this->idDonacion, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
