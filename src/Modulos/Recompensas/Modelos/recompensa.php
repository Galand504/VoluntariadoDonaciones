<?php

namespace App\Modulos\Recompensas\Modelos;

use App\Base\Database;
use Exception;
use PDO;
use PDOException;
use App\Configuracion\Security;

class Recompensa {
    /**
     * Obtiene los donadores estrella por proyecto
     */
    public static function obtenerDonadoresEstrellaPorProyecto($idProyecto) {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_obtener_donadores_estrella_proyecto(?)");
            $stmt->execute([$idProyecto]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerDonadoresEstrellaPorProyecto: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Obtiene la lista general de donadores estrella
     */
    public static function obtenerDonadoresEstrellaGeneral() {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_obtener_donadores_estrella_general()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerDonadoresEstrellaGeneral: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Registra una nueva recompensa
     */
    public static function registrarRecompensa($descripcion, $montoMinimo, $moneda, $fechaEntregaEstimada, $idProyecto) {
        try {
            $con = Database::getConnection();
            
            $sql = "INSERT INTO recompensa (descripcion, montoMinimo, moneda, fechaEntregaEstimada, idProyecto, aprobada) 
                   VALUES (?, ?, ?, ?, ?, 'Pendiente')";
            
            $stmt = $con->prepare($sql);
            $stmt->execute([$descripcion, $montoMinimo, $moneda, $fechaEntregaEstimada, $idProyecto]);
            
            $idRecompensa = $con->lastInsertId();
            
            return [
                'idRecompensa' => $idRecompensa,
                'descripcion' => $descripcion,
                'montoMinimo' => $montoMinimo,
                'moneda' => $moneda,
                'fechaEntregaEstimada' => $fechaEntregaEstimada,
                'idProyecto' => $idProyecto,
                'aprobada' => 'Pendiente'
            ];
            
        } catch (PDOException $e) {
            error_log("Error al registrar recompensa: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Aprueba o rechaza una recompensa
     */
    public static function aprobarRecompensa($idRecompensa, $estado) {
        try {
            $con = Database::getConnection();
            
            if (!in_array($estado, ['Aprobada', 'Rechazada'])) {
                throw new Exception("Estado de aprobación inválido");
            }

            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->rol) || $tokenData->data->rol !== 'Administrador') {
                throw new Exception("No tienes permisos para aprobar recompensas");
            }

            $idAdmin = $tokenData->data->id;
            if (!$idAdmin) {
                throw new Exception("No se pudo obtener el ID del administrador");
            }

            $stmt = $con->prepare("CALL sp_aprobar_recompensa(?, ?, ?)");
            $stmt->execute([$idRecompensa, $estado, $idAdmin]);
            $stmt->closeCursor();
            
            $sql = "SELECT r.*, p.titulo as nombre_proyecto
                    FROM recompensa r
                    JOIN proyecto p ON r.idProyecto = p.idProyecto
                    WHERE r.idRecompensa = ?";
            
            $stmtInfo = $con->prepare($sql);
            $stmtInfo->execute([$idRecompensa]);
            return $stmtInfo->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en aprobarRecompensa: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Actualiza el estado de entrega de una recompensa asignada
     */
    public static function actualizarEstadoEntrega($idRecompensa, $idUsuario, $estadoEntrega, $idAdmin) {
        try {
            $con = Database::getConnection();
            
            // Llamar al procedimiento almacenado
            $stmt = $con->prepare("CALL sp_actualizar_estado_recompensa_usuario(?, ?, ?, ?)");
            $stmt->execute([$idRecompensa, $idUsuario, $estadoEntrega, $idAdmin]);
            
            // Obtener la información actualizada
            $sqlInfo = "SELECT ru.*, r.descripcion,
                           CASE 
                               WHEN u.Tipo = 'Persona' THEN CONCAT(p.Nombre, ' ', p.Apellido)
                               WHEN u.Tipo = 'Empresa' THEN e.nombreEmpresa
                           END as nombre_donante,
                           pr.titulo as nombre_proyecto
                    FROM recompensa_usuario ru
                    JOIN recompensa r ON ru.idRecompensa = r.idRecompensa
                    JOIN usuario u ON ru.idUsuario = u.id_usuario
                    LEFT JOIN persona p ON u.id_usuario = p.id_usuario
                    LEFT JOIN empresa e ON u.id_usuario = e.id_usuario
                    JOIN proyecto pr ON r.idProyecto = pr.idProyecto
                    WHERE ru.idRecompensa = ? AND ru.idUsuario = ?";
            
            $stmtInfo = $con->prepare($sqlInfo);
            $stmtInfo->execute([$idRecompensa, $idUsuario]);
            
            return $stmtInfo->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar estado de recompensa: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Obtiene las recompensas asignadas a usuarios
     */
    public static function obtenerRecompensasAsignadas($filtros = []) {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_obtener_recompensas_asignadas(?, ?, ?)");
            $stmt->execute([
                $filtros['estadoEntrega'] ?? null,
                $filtros['idUsuario'] ?? null,
                $filtros['idProyecto'] ?? null
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener recompensas asignadas: " . $e->getMessage());
            throw new Exception("Error al obtener la lista de recompensas asignadas");
        }
    }
}
