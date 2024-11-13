<?php
class Persona {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Registrar una nueva persona
    public function registrarPersona($DNI, $nombre, $apellido, $edad, $telefono, $idUsuario) {
        try {
            $query = "INSERT INTO persona (DNI, Nombre, Apellido, Edad, Telefono, IdUsuario) VALUES (:DNI, :Nombre, :Apellido, :Edad, :Telefono, :IdUsuario)";
            $stmt = $this->db->prepare($query);

            // Vincular los valores
            $stmt->bindParam(':DNI', $DNI);
            $stmt->bindParam(':Nombre', $nombre);
            $stmt->bindParam(':Apellido', $apellido);
            $stmt->bindParam(':Edad', $edad);
            $stmt->bindParam(':Telefono', $telefono);
            $stmt->bindParam(':IdUsuario', $idUsuario);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            echo "Error al registrar persona: " . $e->getMessage();
            return false;
        }
    }

    // Obtener una persona por ID
    public function obtenerPersona($id) {
        try {
            $query = "SELECT * FROM persona WHERE IdPersona = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $persona = $stmt->fetch(PDO::FETCH_ASSOC);
            return $persona ? $persona : null;
        } catch (Exception $e) {
            echo "Error al obtener persona: " . $e->getMessage();
            return null;
        }
    }
}
?>
