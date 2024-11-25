<?php
class Pago {
    // Conexión a la base de datos y nombre de la tabla
    private $conn;
    private $table_name = "pago";
   
    // Propiedades de la clase Pago
    public $idPago;
    public $fecha;
    public $monto;
    public $estado;
    public $idDonacion;
    public $id_metodopago;
    public $referencia_externa;
    public $moneda;

    // Constructor con la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear un pago
    public function crearPago() {
        if (!$this->validarDatos()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (fecha, monto, estado, idDonacion, id_metodopago, referencia_externa, moneda) 
                  VALUES (:fecha, :monto, :estado, :idDonacion, :id_metodopago, :referencia_externa, :moneda)";
        $stmt = $this->conn->prepare($query);

        // Enlazar los parámetros
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':monto', $this->monto);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':idDonacion', $this->idDonacion);
        $stmt->bindParam(':id_metodopago', $this->id_metodopago);
        $stmt->bindParam(':referencia_externa', $this->referencia_externa);
        $stmt->bindParam(':moneda', $this->moneda);

        if ($stmt->execute()) {
            $this->idPago = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Método para actualizar un pago
    public function actualizarPago() {
        if (!$this->validarDatos() || !$this->idPago) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET fecha = :fecha, monto = :monto, estado = :estado, 
                      idDonacion = :idDonacion, id_metodopago = :id_metodopago, 
                      referencia_externa = :referencia_externa, moneda = :moneda 
                  WHERE idPago = :idPago";
        $stmt = $this->conn->prepare($query);

        // Enlazar los parámetros
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':monto', $this->monto);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':idDonacion', $this->idDonacion);
        $stmt->bindParam(':id_metodopago', $this->id_metodopago);
        $stmt->bindParam(':referencia_externa', $this->referencia_externa);
        $stmt->bindParam(':moneda', $this->moneda);
        $stmt->bindParam(':idPago', $this->idPago);

        return $stmt->execute();
    }

    // Método para eliminar un pago (eliminación lógica)
    public function eliminarPago() {
        if (!$this->idPago) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . " SET estado = 'Fallido' WHERE idPago = :idPago";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPago', $this->idPago);

        return $stmt->execute();
    }

    // Método para obtener una lista de pagos
    public function obtenerPagos($filtro = []) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";

        if (!empty($filtro['estado'])) {
            $query .= " AND estado = :estado";
        }
        if (!empty($filtro['moneda'])) {
            $query .= " AND moneda = :moneda";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($filtro['estado'])) {
            $stmt->bindParam(':estado', $filtro['estado']);
        }
        if (!empty($filtro['moneda'])) {
            $stmt->bindParam(':moneda', $filtro['moneda']);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para validar los datos
    private function validarDatos() {
        if (empty($this->monto) || $this->monto <= 0) {
            return false;
        }
        if (!in_array($this->estado, ['Completado', 'Pendiente', 'Fallido'])) {
            return false;
        }
        if (!in_array($this->moneda, ['USD', 'EUR', 'MXN', 'HNL'])) {
            return false;
        }
        if (empty($this->idDonacion) || empty($this->id_metodopago)) {
            return false;
        }
        return true;
    }

    // Método para ejecutar un procedimiento almacenado
    public function ejecutarProcedimiento($nombreProcedimiento, $parametros = []) {
        try {
            // Preparar la llamada al procedimiento almacenado
            $query = "CALL " . $nombreProcedimiento . "(" . implode(", ", array_fill(0, count($parametros), "?")) . ")";
            $stmt = $this->conn->prepare($query);

            // Enlazar los parámetros
            foreach ($parametros as $index => $valor) {
                $stmt->bindValue($index + 1, $valor);
            }

            // Ejecutar el procedimiento
            $stmt->execute();

            // Verificar si hay resultados
            if ($stmt->columnCount() > 0) {
                // Retornar los resultados en forma de array
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return true; // Procedimiento ejecutado correctamente sin resultados
        } catch (PDOException $e) {
            // Manejar errores
            error_log("Error ejecutando procedimiento almacenado: " . $e->getMessage());
            return false;
        }
    }

    // Método para preparar y ejecutar un procedimiento almacenado específico (ejemplo)
    public function procesarPago() {
        if (!$this->idPago || !$this->monto) {
            return false;
        }

        // Llamar al procedimiento almacenado con los parámetros necesarios
        $resultado = $this->ejecutarProcedimiento("ProcesarPago", [
            $this->idPago,
            $this->monto,
            $this->moneda
        ]);

        return $resultado;
    }
}
?>
