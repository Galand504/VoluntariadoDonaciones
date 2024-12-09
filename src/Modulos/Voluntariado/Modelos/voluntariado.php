<?php

namespace App\Modulos\Voluntariado\Modelos;

use App\Base\Database;
use PDO;
use Exception;

class Voluntariado {
    /**
     * Vincula un usuario como voluntario a un proyecto
     */
    public static function vincular($idUsuario, $idProyecto, $disponibilidad): int {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_registrar_voluntariado(?, ?, ?)");
            $stmt->execute([
                $idUsuario,
                $idProyecto,
                $disponibilidad
            ]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$resultado || !isset($resultado['idVoluntario'])) {
                throw new Exception("Error al registrar voluntariado");
            }
            
            return (int)$resultado['idVoluntario'];
            
        } catch (Exception $e) {
            error_log("Error en Voluntariado::vincular - " . $e->getMessage());
            throw new Exception("Error al registrar el voluntariado");
        }
    }

    /**
     * Verifica si un usuario ya está vinculado a un proyecto
     */
    public static function existeVinculacion($idUsuario, $idProyecto): bool {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_verificar_vinculacion_voluntario(?, ?)");
            $stmt->execute([$idUsuario, $idProyecto]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (bool)($resultado['existe'] ?? false);
            
        } catch (Exception $e) {
            error_log("Error en Voluntariado::existeVinculacion - " . $e->getMessage());
            throw new Exception("Error al verificar vinculación");
        }
    }

    /**
     * Verifica si un proyecto es de tipo Voluntariado
     */
    public static function esProyectoVoluntariado($idProyecto): bool {
        try {
            $con = Database::getConnection();
            $stmt = $con->prepare("CALL sp_verificar_tipo_proyecto(?)");
            $stmt->execute([$idProyecto]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['es_voluntariado'] ?? false;
            
        } catch (Exception $e) {
            error_log("Error en Voluntariado::esProyectoVoluntariado - " . $e->getMessage());
            throw new Exception("Error al verificar tipo de proyecto");
        }
    }

   /**
 * Lista los voluntarios de un proyecto
 * Solo accesible para el organizador del proyecto
 */
public static function listarVoluntarios($idProyecto, $idUsuario): array {
    try {
        $con = Database::getConnection();
        
        // Llamar al procedimiento almacenado
        $stmt = $con->prepare("CALL sp_listar_voluntarios(?, ?)");
        $stmt->execute([$idProyecto, $idUsuario]);
        
        $voluntarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear los datos
        return array_map(function($voluntario) {
            return [
                'id' => $voluntario['idVoluntario'],
                'disponibilidad' => $voluntario['disponibilidad'],
                'email' => $voluntario['email'],
                'tipo' => $voluntario['tipo_voluntario'],
                'nombre_completo' => $voluntario['tipo_voluntario'] === 'Persona' 
                    ? $voluntario['nombre'] . ' ' . $voluntario['apellido']
                    : $voluntario['nombre'],
                'identificacion' => $voluntario['identificacion'],
                'telefono' => $voluntario['telefono'],
                'razon_social' => $voluntario['razonSocial'] ?? null
            ];
        }, $voluntarios);
        
    } catch (Exception $e) {
        error_log("Error en Voluntariado::listarVoluntarios - " . $e->getMessage());
        throw new Exception($e->getMessage());
    }
}

/**
 * Elimina un voluntario del proyecto
 * Solo el organizador puede eliminar voluntarios
 */
public static function eliminar($idVoluntario, $idUsuario): void {
    try {
        $con = Database::getConnection();
        
        // Llamar al procedimiento almacenado
        $stmt = $con->prepare("CALL sp_eliminar_voluntario(?, ?)");
        $stmt->execute([
            $idVoluntario,
            $idUsuario
        ]);
        
    } catch (Exception $e) {
        error_log("Error en Voluntariado::eliminar - " . $e->getMessage());
        throw new Exception($e->getMessage());
    }
}
}

?>