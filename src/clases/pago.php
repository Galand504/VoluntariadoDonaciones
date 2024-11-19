<?php
class Pago {
    // Conexión a la base de datos y nombre de la tabla
    private $conn;
    private $table_name = "pagos";

    // Propiedades de la clase Pago
    public $idPago;
    public $idDonacion;
    public $montoPago;
    public $fechaPago;
    public $metodoPago;

    // Constructor con la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear un pago
    public function crearPago() {
        $query = "INSERT INTO " . $this->table_name . " (idDonacion, montoPago, fechaPago, metodoPago) 
                  VALUES (:idDonacion, :montoPago, :fechaPago, :metodoPago)";
        $stmt = $this->conn->prepare($query);

        // Sanitizar y enlazar los parámetros
        $this->montoPago = htmlspecialchars(strip_tags($this->montoPago));
        $this->fechaPago = htmlspecialchars(strip_tags($this->fechaPago));
        $this->metodoPago = htmlspecialchars(strip_tags($this->metodoPago));

        $stmt->bindParam(':idDonacion', $this->idDonacion);
        $stmt->bindParam(':montoPago', $this->montoPago);
        $stmt->bindParam(':fechaPago', $this->fechaPago);
        $stmt->bindParam(':metodoPago', $this->metodoPago);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para obtener el pago asociado a una donación
    public function obtenerPago($idDonacion = null) {
        $query = "SELECT idPago, idDonacion, montoPago, fechaPago, metodoPago 
                  FROM " . $this->table_name . " 
                  WHERE idDonacion = :idDonacion 
                  LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        // Verificar si se proporcionó un ID de donación
        $idDonacion = $idDonacion ?? $this->idDonacion;

        // Enlazar parámetros
        $stmt->bindParam(':idDonacion', $idDonacion);
        
        // Ejecutar la consulta
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Asignar propiedades
            $this->idPago = $row['idPago'];
            $this->idDonacion = $row['idDonacion'];
            $this->montoPago = $row['montoPago'];
            $this->fechaPago = $row['fechaPago'];
            $this->metodoPago = $row['metodoPago'];

            // Devolver los datos del pago como array
            return [
                "idPago" => $this->idPago,
                "montoPago" => $this->montoPago,
                "fechaPago" => $this->fechaPago,
                "metodoPago" => $this->metodoPago
            ];
        }

        // Si no se encuentra el pago, devolver null
        return null;
    }
}
?>

