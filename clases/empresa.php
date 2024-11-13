<?php
class Empresa {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Registrar una nueva empresa
    public function registrarEmpresa($direccion, $telefono, $idUsuario) {
        try {
            $query = "INSERT INTO empresa (Direccion, Telefono, Id_Usuario) VALUES (:Direccion, :Telefono, :Id_Usuario)";
            $stmt = $this->db->prepare($query);

            // Vincular los valores
            $stmt->bindParam(':Direccion', $direccion);
            $stmt->bindParam(':Telefono', $telefono);
            $stmt->bindParam(':Id_Usuario', $idUsuario);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            echo "Error al registrar empresa: " . $e->getMessage();
            return false;
        }
    }

    // Obtener una empresa por ID
    public function obtenerEmpresa($id) {
        try {
            $query = "SELECT * FROM empresa WHERE ID_Empresa = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
            return $empresa ? $empresa : null;
        } catch (Exception $e) {
            echo "Error al obtener empresa: " . $e->getMessage();
            return null;
        }
    }
}
?>
