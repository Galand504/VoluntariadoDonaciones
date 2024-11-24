<?php

namespace App\Controllers;

use App\Models\Proyecto;

class ProyectoController {
    private $conexion;
    private $proyecto;

    // Constructor que recibe la conexión a la base de datos
    public function __construct($db) {
        $this->conexion = $db;
        $this->proyecto = new ProyectoController($db); // Instancia de la clase Proyecto
    }

    // Método para mostrar todos los proyectos
    public function leerProyectos() {
        $query = "SELECT * FROM proyecto";
        $result = $this->conexion->query($query);
        return $result;
    }

        // Si hay proyectos, agregarlos al arreglo
        while ($row = $result->fetch_assoc()) {
            $proyectos[] = $row;
        }

        return $proyectos;
    }

    // Método para crear un nuevo proyecto
    public function crearProyecto($titulo, $descripcion, $objetivo, $presupuesto, $fechaEntregaEstimada, $idUsuario, $idRecompensa, $idRiesgo) {
        $this->proyectos->setTitulo($titulo);
        $this->proyectos->setDescripcion($descripcion);
        $this->proyectos->setObjetivo($objetivo);
        $this->proyectos->setPresupuesto($presupuesto);
        $this->proyectos->setFechaEntregaEstimada($fechaEntregaEstimada);
        $this->proyectos->setIdUsuario($idUsuario);
        $this->proyectos->setIdRecompensa($idRecompensa);
        $this->proyectos->setIdRiesgo($idRiesgo);

        if ($this->proyectos->crearProyecto()) {
            return "Proyecto creado exitosamente.";
        } else {
            return "Error al crear el proyecto.";
        }
    }

    // Método para actualizar un proyecto existente
    public function actualizarProyecto($idProyecto, $titulo, $descripcion, $objetivo, $presupuesto, $fechaEntregaEstimada, $idUsuario, $idRecompensa, $idRiesgo) {
        $this->proyecto->setIdProyecto($idProyecto);
        $this->proyecto->setTitulo($titulo);
        $this->proyecto->setDescripcion($descripcion);
        $this->proyecto->setObjetivo($objetivo);
        $this->proyecto->setPresupuesto($presupuesto);
        $this->proyecto->setFechaEntregaEstimada($fechaEntregaEstimada);
        $this->proyecto->setIdUsuario($idUsuario);
        $this->proyecto->setIdRecompensa($idRecompensa);
        $this->proyecto->setIdRiesgo($idRiesgo);

        if ($this->proyecto->actualizarProyecto()) {
            return "Proyecto actualizado exitosamente.";
        } else {
            return "Error al actualizar el proyecto.";
        }
    }

    // Método para eliminar un proyecto
    public function eliminarProyecto($idProyecto) {
        $this->proyecto->setIdProyecto($idProyecto);

        if ($this->proyecto->eliminarProyecto()) {
            return "Proyecto eliminado exitosamente.";
        } else {
            return "Error al eliminar el proyecto.";
        }
    }

    // Método para obtener un proyecto por su ID
    public function obtenerProyecto($idProyecto) {
        $this->proyecto->setIdProyecto($idProyecto);
        return $this->proyecto->obtenerProyectoPorId();
    }
}

