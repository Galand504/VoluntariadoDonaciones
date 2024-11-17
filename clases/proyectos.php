<?php
class Proyecto {
    // Propiedades
    private $idProyecto;
    private $titulo;
    private $descripcion;
    private $objetivo;
    private $presupuesto;
    private $fechaEntregaEstimada;
    private $idUsuario;
    private $idRecompensa;
    private $idRiesgo;
    private $conexion;

    // Constructor
    public function __construct($db) {
        $this->conexion = $db;
    }

    // Métodos Setters
    public function setIdProyecto($idProyecto) {
        $this->idProyecto = $idProyecto;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setObjetivo($objetivo) {
        $this->objetivo = $objetivo;
    }

    public function setPresupuesto($presupuesto) {
        $this->presupuesto = $presupuesto;
    }

    public function setFechaEntregaEstimada($fechaEntregaEstimada) {
        $this->fechaEntregaEstimada = $fechaEntregaEstimada;
    }

    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    public function setIdRecompensa($idRecompensa) {
        $this->idRecompensa = $idRecompensa;
    }

    public function setIdRiesgo($idRiesgo) {
        $this->idRiesgo = $idRiesgo;
    }

    // Métodos CRUD
    // Crear Proyecto
    public function crearProyecto() {
        $query = "INSERT INTO proyecto (titulo, descripcion, objetivo, presupuesto, fechaEntregaEstimada, idUsuario, idRecompensa, idRiesgo)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sssdsiis", $this->titulo, $this->descripcion, $this->objetivo, $this->presupuesto,
                          $this->fechaEntregaEstimada, $this->idUsuario, $this->idRecompensa, $this->idRiesgo);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer Proyectos
    public function leerProyectos() {
        $query = "SELECT * FROM proyecto";
        $result = $this->conexion->query($query);
        return $result;
    }

    // Actualizar Proyecto
    public function actualizarProyecto() {
        $query = "UPDATE proyecto SET titulo=?, descripcion=?, objetivo=?, presupuesto=?, 
                  fechaEntregaEstimada=?, idUsuario=?, idRecompensa=?, idRiesgo=?
                  WHERE idProyecto=?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sssdsiisi", $this->titulo, $this->descripcion, $this->objetivo, $this->presupuesto,
                          $this->fechaEntregaEstimada, $this->idUsuario, $this->idRecompensa, $this->idRiesgo, $this->idProyecto);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Proyecto
    public function eliminarProyecto() {
        $query = "DELETE FROM proyecto WHERE idProyecto=?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $this->idProyecto);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtener Proyecto por ID
    public function obtenerProyectoPorId() {
        $query = "SELECT * FROM proyecto WHERE idProyecto=?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $this->idProyecto);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
