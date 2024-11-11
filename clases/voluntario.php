<?php

class Voluntario {
    private $db;
    private $id;
    private $nombre;
    private $email;

    // Constructor para inicializar la conexión a la base de datos
    public function __construct($dbConnection, $id = null) {
        $this->db = $dbConnection;
        if ($id) {
            $this->id = $id;
            $this->cargarVoluntario();
        }
    }

    // Cargar los datos del voluntario desde la base de datos
    private function cargarVoluntario() {
        $query = "SELECT * FROM voluntarios WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $voluntario = $result->fetch_assoc();
            $this->nombre = $voluntario['nombre'];
            $this->email = $voluntario['email'];
        } else {
            throw new Exception("Voluntario no encontrado.");
        }
    }

    // Función para registrar un nuevo voluntario
    public static function registrarVoluntario($dbConnection, $nombre, $email) {
        $query = "INSERT INTO voluntarios (nombre, email) VALUES (?, ?)";
        $stmt = $dbConnection->prepare($query);
        $stmt->bind_param("ss", $nombre, $email);
        if ($stmt->execute()) {
            return $dbConnection->insert_id; // Retorna el ID del voluntario recién creado
        }
        return false;
    }

    // Función para ver las organizaciones disponibles
    public function verOrganizaciones() {
        $query = "SELECT * FROM organizaciones";
        $result = $this->db->query($query);
        $organizaciones = [];
        while ($row = $result->fetch_assoc()) {
            $organizaciones[] = $row;
        }
        return $organizaciones;
    }

    // Función para realizar una donación (voluntariado en tiempo, dinero o productos)
    public function donar($organizacionId, $monto, $tipo) {
        // Verificar que la organización exista
        $query = "SELECT * FROM organizaciones WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $organizacionId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            throw new Exception("La organización seleccionada no existe.");
        }

        // Registrar la donación
        $query = "INSERT INTO donaciones (voluntario_id, organizacion_id, monto, tipo, fecha) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiis", $this->id, $organizacionId, $monto, $tipo);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Función para obtener el historial de donaciones
    public function obtenerDonaciones() {
        $query = "SELECT donaciones.*, organizaciones.nombre AS organizacion_nombre FROM donaciones
                  JOIN organizaciones ON donaciones.organizacion_id = organizaciones.id
                  WHERE donaciones.voluntario_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $donaciones = [];
        while ($row = $result->fetch_assoc()) {
            $donaciones[] = $row;
        }
        return $donaciones;
    }

    // Función para obtener los datos del voluntario
    public function obtenerDatos() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email
        ];
    }
}

?>




/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

