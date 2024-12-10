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
    public static function crearProyecto($titulo, $descripcion, $objetivo, $meta, $moneda, $tipo_actividad, $id_usuario): array {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_agregar_proyecto(?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $titulo,
                $descripcion,
                $objetivo,
                $meta,
                $moneda,
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
    public static function actualizarProyecto($idProyecto, $titulo, $descripcion, $objetivo, $meta, $moneda, $estado, $tipo_actividad, $id_usuario): array {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_actualizar_proyecto(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $idProyecto,
                $titulo,
                $descripcion,
                $objetivo,
                $meta,
                $moneda,
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
     * @param int $idProyecto ID del proyecto a eliminar
     * @param int $id_usuario ID del usuario que solicita la eliminación
     * @return bool true si se eliminó correctamente
     * @throws Exception si hay error en la eliminación
     */
    public static function eliminarProyecto($idProyecto, $id_usuario): bool {
        try {
            $con = Database::getConnection();
            
            // Primero verificar si el proyecto existe
            $checkStmt = $con->prepare("SELECT COUNT(*) FROM proyecto WHERE idProyecto = ?");
            $checkStmt->execute([$idProyecto]);
            $exists = $checkStmt->fetchColumn();
            
            if (!$exists) {
                throw new Exception("El proyecto con ID $idProyecto no existe");
            }
            
            // Si existe, proceder con la eliminación
            $stmt = $con->prepare("CALL sp_eliminar_proyecto(?, ?)");
            $stmt->execute([$idProyecto, $id_usuario]);
            
            // El procedimiento puede lanzar excepciones con mensajes específicos
            // que debemos capturar y manejar apropiadamente
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si el proyecto fue marcado como cancelado, considerarlo como éxito
            if (strpos($resultado['@mensaje'] ?? '', 'marcado como cancelado') !== false) {
                return true;
            }
            
            return true;
            
        } catch (PDOException $e) {
            // Capturar mensajes específicos del procedimiento
            if (strpos($e->getMessage(), 'No tienes permisos') !== false) {
                throw new Exception("No tienes permisos para eliminar este proyecto");
            }
            if (strpos($e->getMessage(), 'marcado como cancelado') !== false) {
                throw new Exception($e->getMessage());
            }
            
            error_log("Error en eliminarProyecto: " . $e->getMessage());
            throw new Exception("Error al eliminar el proyecto: " . $e->getMessage());
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

    /**
     * Obtiene un proyecto específico
     * @param int $idProyecto ID del proyecto
     * @param int $id_usuario ID del usuario que hace la solicitud
     * @return array Datos del proyecto
     * @throws Exception
     */
    public static function obtenerProyecto($idProyecto, $id_usuario): array {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_obtener_proyecto(?, ?)");
            $stmt->execute([$idProyecto, $id_usuario]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultado) {
                throw new Exception("No se encontró el proyecto especificado");
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerProyecto: " . $e->getMessage());
            
            // Si es un error de permisos (SQLSTATE 45000)
            if ($e->getCode() == '45000') {
                throw new Exception($e->getMessage());
            }
            
            throw new Exception("Error al obtener el proyecto");
        }
    }
}
?>
