<?php

namespace App\Modulos\Proyectos\Modelos;

use App\Base\Database;
use Exception;
use PDO;
use PDOException;

class Proyecto {
    /**
     * Obtiene las actividades (proyectos) con filtro opcional por tipo
     */
    public static function obtenerActividades($tipo = null): array {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_get_actividades(?)");
            $stmt->execute([$tipo]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
        } catch (PDOException $e) {
            error_log("Error en obtenerActividades: " . $e->getMessage());
            throw new Exception("Error al obtener las actividades");
        }
    }

    /**
     * Crea un nuevo proyecto
     */
    public static function crearProyecto($titulo, $descripcion, $objetivo, $meta, $tipo_actividad, $id_usuario): array {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_agregar_proyecto(?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $titulo,
                $descripcion,
                $objetivo,
                $meta,
                $tipo_actividad,
                $id_usuario
            ]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$resultado) {
                throw new Exception("Error al crear el proyecto");
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error en crearProyecto: " . $e->getMessage());
            throw new Exception("Error al crear el proyecto");
        }
    }

    /**
     * Actualiza un proyecto existente
     */
    public static function actualizarProyecto($idProyecto, $titulo, $descripcion, $objetivo, $meta, $estado, $tipo_actividad, $id_usuario): array {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_actualizar_proyecto(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $idProyecto,
                $titulo,
                $descripcion,
                $objetivo,
                $meta,
                $estado,
                $tipo_actividad,
                $id_usuario
            ]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$resultado) {
                throw new Exception("Error al actualizar el proyecto");
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error en actualizarProyecto: " . $e->getMessage());
            throw new Exception("Error al actualizar el proyecto");
        }
    }

    /**
     * Elimina un proyecto
     */
    public static function eliminarProyecto($idProyecto, $id_usuario): bool {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_eliminar_proyecto(?, ?)");
            $stmt->execute([$idProyecto, $id_usuario]);
            return true;
            
        } catch (PDOException $e) {
            error_log("Error en eliminarProyecto: " . $e->getMessage());
            throw new Exception("Error al eliminar el proyecto");
        }
    }
    /**
 * Cambia el estado de un proyecto
 */
public static function cambiarEstadoProyecto($idProyecto, $estado, $id_usuario): array {
    try {
        $con = Database::getConnection();
        
        $stmt = $con->prepare("CALL sp_cambiar_estado_proyecto(?, ?, ?)");
        $stmt->execute([
            $idProyecto,
            $estado,
            $id_usuario
        ]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$resultado) {
            throw new Exception("Error al cambiar el estado del proyecto");
        }
        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("Error en cambiarEstadoProyecto: " . $e->getMessage());
        throw new Exception("Error al cambiar el estado del proyecto");
    }
}
}
?>
