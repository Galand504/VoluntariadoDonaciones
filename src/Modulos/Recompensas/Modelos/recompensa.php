<?php

namespace App\Modulos\Recompensas\Modelos;

use App\Base\Database;
use PDO;
use PDOException;

class recompensa {
    private static int $criterioCantidad = 5;
    private static int $criterioMonto = 1000;

    public static function obtenerDonadoresEstrella() {
        try {
            $con = Database::getConnection();
            
            $sql = "SELECT u.id_usuario, 
                           COUNT(d.idDonacion) as cantidad_donaciones,
                           SUM(d.monto) as monto_total
                    FROM usuarios u
                    JOIN donaciones d ON u.id_usuario = d.id_usuario
                    JOIN pagos p ON d.idDonacion = p.idDonacion
                    WHERE p.estado = 'Completado'
                    GROUP BY u.id_usuario
                    HAVING cantidad_donaciones >= :criterio_cantidad 
                       OR monto_total >= :criterio_monto";

            $stmt = $con->prepare($sql);
            $stmt->execute([
                ':criterio_cantidad' => self::$criterioCantidad,
                ':criterio_monto' => self::$criterioMonto
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener donadores estrella: " . $e->getMessage());
            return [];
        }
    }

    public static function registrarRecompensa($idUsuario, $tipoRecompensa) {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("CALL RegistrarRecompensa(?, ?)");
            $stmt->execute([$idUsuario, $tipoRecompensa]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Error al registrar recompensa: " . $e->getMessage());
            return false;
        }
    }

    public static function obtenerRecompensasPorUsuario($idUsuario) {
        try {
            $con = Database::getConnection();
            
            $stmt = $con->prepare("SELECT * FROM recompensas WHERE id_usuario = ?");
            $stmt->execute([$idUsuario]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener recompensas del usuario: " . $e->getMessage());
            return false;
        }
    }

    public static function verificarElegibilidad($idUsuario) {
        try {
            $con = Database::getConnection();
            
            $sql = "SELECT COUNT(d.idDonacion) as cantidad_donaciones,
                           SUM(d.monto) as monto_total
                    FROM donaciones d
                    JOIN pagos p ON d.idDonacion = p.idDonacion
                    WHERE d.id_usuario = :id_usuario
                    AND p.estado = 'Completado'";

            $stmt = $con->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'elegible' => ($resultado['cantidad_donaciones'] >= self::$criterioCantidad || 
                              $resultado['monto_total'] >= self::$criterioMonto),
                'estadisticas' => [
                    'cantidad_donaciones' => $resultado['cantidad_donaciones'],
                    'monto_total' => $resultado['monto_total']
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error al verificar elegibilidad: " . $e->getMessage());
            return false;
        }
    }
}
