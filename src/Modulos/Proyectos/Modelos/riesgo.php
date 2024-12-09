<?php

namespace App\Modulos\Riesgo\Modelos;

use App\Base\Database;
use PDO;
use Exception;

class Riesgo {
    
    /**
     * Registra un nuevo riesgo
     */
    public static function registrar($descripcion, $planMitigacion, $idProyecto, $idUsuario) {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_registrar_riesgo(?, ?, ?, ?)");
            $stmt->execute([
                $descripcion,
                $planMitigacion,
                $idProyecto,
                $idUsuario
            ]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Riesgo::registrar - " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Actualiza un riesgo existente
     */
    public static function actualizar($idRiesgo, $descripcion, $planMitigacion, $idUsuario) {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_actualizar_riesgo(?, ?, ?, ?)");
            $stmt->execute([
                $idRiesgo,
                $descripcion,
                $planMitigacion,
                $idUsuario
            ]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Riesgo::actualizar - " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Elimina un riesgo
     */
    public static function eliminar($idRiesgo, $idUsuario) {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_eliminar_riesgo(?, ?)");
            $stmt->execute([
                $idRiesgo,
                $idUsuario
            ]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Riesgo::eliminar - " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Lista los riesgos de un proyecto
     */
    public static function listarPorProyecto($idProyecto) {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_obtener_riesgos_proyecto(?)");
            $stmt->execute([$idProyecto]);
            
            $riesgos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Formatear las fechas y datos si es necesario
            return array_map(function($riesgo) {
                return [
                    'id' => $riesgo['idRiesgo'],
                    'descripcion' => $riesgo['descripcion'],
                    'planMitigacion' => $riesgo['planMitigacion'],
                    'fecha_registro' => $riesgo['fecha_registro'],
                    'fecha_actualizacion' => $riesgo['fecha_actualizacion'],
                    'proyecto' => [
                        'titulo' => $riesgo['proyecto_titulo']
                    ],
                    'organizador' => [
                        'email' => $riesgo['organizador_email']
                    ]
                ];
            }, $riesgos);
            
        } catch (Exception $e) {
            error_log("Error en Riesgo::listarPorProyecto - " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}