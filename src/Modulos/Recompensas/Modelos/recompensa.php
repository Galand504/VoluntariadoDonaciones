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
     * Registra una nueva recompensa
     */
    public static function registrarRecompensa($descripcion, $montoMinimo, $fechaEntregaEstimada, $idProyecto) {
        try {
            $con = Database::getConnection();
            
            $sql = "INSERT INTO recompensa (descripcion, montoMinimo, fechaEntregaEstimada, idProyecto, aprobada) 
                   VALUES (?, ?, ?, ?, 'Pendiente')";
            
            $stmt = $con->prepare($sql);
            $stmt->execute([$descripcion, $montoMinimo, $fechaEntregaEstimada, $idProyecto]);
            
            $idRecompensa = $con->lastInsertId();
            
            return [
                'idRecompensa' => $idRecompensa,
                'descripcion' => $descripcion,
                'montoMinimo' => $montoMinimo,
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
     * Verifica y asigna recompensas automáticamente
     */
    public static function verificarYAsignarRecompensas($idUsuario, $idProyecto, $idDonacion) {
        try {
            $con = Database::getConnection();
            
            // 1. Obtener el monto total de la donación
            $sqlMonto = "SELECT SUM(p.monto) as total
                         FROM donacion d
                         JOIN pago p ON d.idDonacion = p.idDonacion
                         WHERE d.idDonacion = ? AND p.estado = 'Completado'";
            
            $stmtMonto = $con->prepare($sqlMonto);
            $stmtMonto->execute([$idDonacion]);
            $montoTotal = $stmtMonto->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // 2. Obtener recompensas disponibles según el monto
            $sqlRecompensas = "SELECT r.idRecompensa, r.descripcion, r.montoMinimo
                              FROM recompensa r
                              WHERE r.idProyecto = ?
                              AND r.aprobada = 'Aprobada'
                              AND r.montoMinimo <= ?
                              AND NOT EXISTS (
                                  SELECT 1 
                                  FROM recompensa_usuario ru 
                                  WHERE ru.idRecompensa = r.idRecompensa 
                                  AND ru.idUsuario = ?
                              )";
            
            $stmtRecompensas = $con->prepare($sqlRecompensas);
            $stmtRecompensas->execute([$idProyecto, $montoTotal, $idUsuario]);
            $recompensasDisponibles = $stmtRecompensas->fetchAll(PDO::FETCH_ASSOC);

            // 3. Asignar recompensas
            $recompensasAsignadas = [];
            foreach ($recompensasDisponibles as $recompensa) {
                try {
                    $sqlAsignar = "INSERT INTO recompensa_usuario 
                                  (idRecompensa, idUsuario, idDonacion, fechaAsignacion) 
                                  VALUES (?, ?, ?, NOW())";
                    
                    $stmtAsignar = $con->prepare($sqlAsignar);
                    $stmtAsignar->execute([
                        $recompensa['idRecompensa'],
                        $idUsuario,
                        $idDonacion
                    ]);
                    
                    $recompensasAsignadas[] = [
                        'idRecompensa' => $recompensa['idRecompensa'],
                        'descripcion' => $recompensa['descripcion'],
                        'montoMinimo' => $recompensa['montoMinimo']
                    ];
                    
                } catch (PDOException $e) {
                    error_log("Error al asignar recompensa: " . $e->getMessage());
                    continue;
                }
            }

            return $recompensasAsignadas;
            
        } catch (PDOException $e) {
            error_log("Error al verificar y asignar recompensas: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}                     
