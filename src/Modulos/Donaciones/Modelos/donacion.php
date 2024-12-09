<?php

namespace App\Modulos\Donaciones\Modelos;

use App\Base\Database;
use PDO;
use Exception;

class donacion {
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
