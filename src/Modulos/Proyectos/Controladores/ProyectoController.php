<?php

namespace App\Controllers;

use App\Models\Proyecto;

class ProyectoController {
    private $conexion;
    private $proyecto;

    // Constructor que recibe la conexión a la base de datos
    public function __construct($db) {
        $this->conexion = $db;
        $this->proyecto = new ProyectoController($db); // Instancia correcta del modelo Proyecto
    }

    // Método para mostrar todos los proyectos
    public function leerProyectos() {
        $query = "SELECT * FROM proyecto";
        $result = $this->conexion->query($query);

        $proyectos = [];  // Inicializa el arreglo para almacenar los proyectos
        while ($row = $result->fetch_assoc()) {
            $proyectos[] = $row; // Agrega cada proyecto al arreglo
        }

        return $proyectos;  // Retorna todos los proyectos
    }

    // Método para crear un nuevo proyecto
    public function crearProyecto($titulo, $descripcion, $objetivo, $presupuesto, $fechaEntregaEstimada, $idUsuario, $idRecompensa, $idRiesgo) {
        // Asigna los valores al modelo Proyecto
        $this->proyecto->setTitulo($titulo);
        $this->proyecto->setDescripcion($descripcion);
        $this->proyecto->setObjetivo($objetivo);
        $this->proyecto->setPresupuesto($presupuesto);
        $this->proyecto->setFechaEntregaEstimada($fechaEntregaEstimada);
        $this->proyecto->setIdUsuario($idUsuario);
        $this->proyecto->setIdRecompensa($idRecompensa);
        $this->proyecto->setIdRiesgo($idRiesgo);

        // Intenta crear el proyecto
        if ($this->proyecto->crearProyecto()) {
            return "Proyecto creado exitosamente.";
        } else {
            return "Error al crear el proyecto.";
        }
    }

    // Método para actualizar un proyecto existente
    public function actualizarProyecto($idProyecto, $titulo, $descripcion, $objetivo, $presupuesto, $fechaEntregaEstimada, $idUsuario, $idRecompensa, $idRiesgo) {
        // Asigna los valores al modelo Proyecto
        $this->proyecto->setIdProyecto($idProyecto);
        $this->proyecto->setTitulo($titulo);
        $this->proyecto->setDescripcion($descripcion);
        $this->proyecto->setObjetivo($objetivo);
        $this->proyecto->setPresupuesto($presupuesto);
        $this->proyecto->setFechaEntregaEstimada($fechaEntregaEstimada);
        $this->proyecto->setIdUsuario($idUsuario);
        $this->proyecto->setIdRecompensa($idRecompensa);
        $this->proyecto->setIdRiesgo($idRiesgo);

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

    // Método para eliminar un proyecto
    public function eliminarProyecto($idProyecto) {
        $this->proyecto->setIdProyecto($idProyecto);

        // Intenta eliminar el proyecto
        if ($this->proyecto->eliminarProyecto()) {
            return "Proyecto eliminado exitosamente.";
        } else {
            return "Error al eliminar el proyecto.";
        }
    }

    // Método para obtener un proyecto por su ID
    public function obtenerProyecto($idProyecto) {
        $this->proyecto->setIdProyecto($idProyecto);
        return $this->proyecto->obtenerProyectoPorId();  // Retorna el proyecto por su ID
    }
}
