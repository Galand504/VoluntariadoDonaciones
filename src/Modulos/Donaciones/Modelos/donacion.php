<?php

namespace App\Modulos\Donaciones\Modelos;

use App\Base\Database;
use PDO;
use Exception;
use PDOException;

class Donacion {
/**
     * Obtiene las donaciones disponibles con su progreso
     * @return array Lista de donaciones con sus detalles y progreso
     * @throws Exception Si hay un error al obtener las donaciones
     */
    public static function obtenerDonacionesConProgreso(): array {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_consultar_progreso_donacion()");
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
        } catch (PDOException $e) {
            error_log("Error en obtenerDonacionesConProgreso: " . $e->getMessage());
            throw new Exception("Error al obtener las donaciones disponibles");
        }
    }

    /**
     * Vincula un usuario con un proyecto de donación
     */
    public static function vincular($id_usuario, $idProyecto): int {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL sp_vincular_donacion(?, ?)");
            $stmt->execute([$id_usuario, $idProyecto]);
            
            // Obtener el ID de la donación generada
            $stmt = $con->query("SELECT LAST_INSERT_ID() as id");
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)$resultado['id'];
            
        } catch (Exception $e) {
            error_log("Error en Donacion::vincular - " . $e->getMessage());
            throw new Exception("Error al vincular la donación");
        }
    }

    /**
     * Verifica si un usuario ya está vinculado a un proyecto
     */
    public static function existeVinculacion($id_usuario, $idProyecto): bool {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("SELECT 1 FROM donacion WHERE id_usuario = ? AND idProyecto = ?");
            $stmt->execute([$id_usuario, $idProyecto]);
            
            return $stmt->fetch() !== false;
            
        } catch (Exception $e) {
            error_log("Error en Donacion::existeVinculacion - " . $e->getMessage());
            throw new Exception("Error al verificar vinculación");
        }
    }
}
?>
