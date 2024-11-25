<?php

namespace App\Controladores;

use App\Configuracion\Voluntario;
use App\Database\DatabaseConnection;
use Exception;

class VoluntarioController
{
    private $voluntario;
    src/Database/DatabaseConnection.php
    public function __construct()
    {
        // Crear la conexión a la base de datos
        $databaseConnection = new DatabaseConnection();
        $db = $databaseConnection->getConnection();

        // Crear una instancia de Voluntario
        $this->voluntario = new Voluntario($db);
    }

    // Registrar un nuevo voluntario
    public function registrarVoluntario($disponibilidad, $idUsuario, $proyecto_idProyecto)
    {
        try {
            $idVoluntariado = Voluntario::registrarVoluntariado(
                $this->voluntario->getDb(), $disponibilidad, $idUsuario, $proyecto_idProyecto
            );
            return "Voluntariado registrado con ID: $idVoluntariado";
        } catch (Exception $e) {
            return "Error al registrar el voluntariado: " . $e->getMessage();
        }
    }

    // Obtener voluntariado por ID
    public function obtenerVoluntario($idVoluntario)
    {
        try {
            $voluntario = new Voluntario($this->voluntario->getDb(), $idVoluntario);
            return $voluntario->obtenerDatos();
        } catch (Exception $e) {
            return "Error al obtener el voluntario: " . $e->getMessage();
        }
    }

    // Actualizar la disponibilidad de un voluntario
    public function actualizarDisponibilidad($idVoluntario, $nuevaDisponibilidad)
    {
        try {
            $voluntario = new Voluntario($this->voluntario->getDb(), $idVoluntario);
            if ($voluntario->actualizarDisponibilidad($nuevaDisponibilidad)) {
                return "Disponibilidad actualizada exitosamente.";
            }
            return "Error al actualizar la disponibilidad.";
        } catch (Exception $e) {
            return "Error al actualizar la disponibilidad: " . $e->getMessage();
        }
    }

    // Eliminar un voluntariado
    public function eliminarVoluntario($idVoluntario)
    {
        try {
            $voluntario = new Voluntario($this->voluntario->getDb(), $idVoluntario);
            if ($voluntario->eliminarVoluntariado()) {
                return "Voluntariado eliminado exitosamente.";
            }
            return "Error al eliminar el voluntariado.";
        } catch (Exception $e) {
            return "Error al eliminar el voluntariado: " . $e->getMessage();
        }
    }

    // Obtener el historial de voluntariados de un usuario
    public function obtenerVoluntariadosPorUsuario($idUsuario)
    {
        try {
            $voluntariados = Voluntario::obtenerVoluntariadosPorUsuario($this->voluntario->getDb(), $idUsuario);
            return $voluntariados;
        } catch (Exception $e) {
            return "Error al obtener los voluntariados: " . $e->getMessage();
        }
    }
}
