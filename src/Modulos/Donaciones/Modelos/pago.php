<?php
namespace App\Modulos\Donaciones\Modelos;

use App\Base\Database;
use PDO;
use PDOException;
use Exception;

class pago {
    /**
     * Crea un nuevo pago usando el procedimiento almacenado RegistrarPago
     */
    public static function crearPago($monto, $estado, $idDonacion, $id_metodopago, $moneda, $referencia_externa) {
        try {
            if (!self::validarDatos($monto, $estado, $moneda, $idDonacion, $id_metodopago)) {
                throw new Exception("Datos de pago inválidos");
            }

            $con = Database::getConnection();
            
            // Llamar al procedimiento almacenado
            $stmt = $con->prepare("CALL RegistrarPago(?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $monto,
                $estado,
                $idDonacion,
                $id_metodopago,
                $moneda,
                $referencia_externa
            ]);

            // El procedimiento devuelve el ID del pago creado
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['idPago'];
            
        } catch (PDOException $e) {
            error_log("Error al crear pago: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Actualiza el estado de un pago
     */
    public static function actualizarEstado($idPago, $estado) {
        try {
            if (!in_array($estado, ['Pendiente', 'Completado', 'Fallido'])) {
                throw new Exception("Estado de pago inválido");
            }

            $con = Database::getConnection();
            
            $sql = "UPDATE pago 
                    SET estado = ?, fecha = NOW() 
                    WHERE idPago = ?";
            
            $stmt = $con->prepare($sql);
            $stmt->execute([$estado, $idPago]);

            if ($stmt->rowCount() > 0) {
                return [
                    'idPago' => $idPago,
                    'estado' => $estado
                ];
            }
            throw new Exception("No se pudo actualizar el estado del pago");
            
        } catch (PDOException $e) {
            error_log("Error al actualizar estado del pago: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Obtiene los detalles de un pago
     */
    public static function obtenerPago($idPago) {
        try {
            $con = Database::getConnection();
            
            $sql = "SELECT p.*, d.id_usuario, d.idProyecto 
                    FROM pago p
                    JOIN donacion d ON p.idDonacion = d.idDonacion
                    WHERE p.idPago = ?";
            
            $stmt = $con->prepare($sql);
            $stmt->execute([$idPago]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener pago: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Obtiene lista de pagos con filtros opcionales
     */
    public static function obtenerPagos($filtros = []) {
        try {
            $con = Database::getConnection();
            
            $sql = "SELECT p.*, d.id_usuario, d.idProyecto 
                    FROM pago p
                    JOIN donacion d ON p.idDonacion = d.idDonacion
                    WHERE 1=1";
            
            $params = [];

            if (isset($filtros['estado'])) {
                $sql .= " AND p.estado = ?";
                $params[] = $filtros['estado'];
            }
            if (isset($filtros['idProyecto'])) {
                $sql .= " AND d.idProyecto = ?";
                $params[] = $filtros['idProyecto'];
            }
            if (isset($filtros['id_usuario'])) {
                $sql .= " AND d.id_usuario = ?";
                $params[] = $filtros['id_usuario'];
            }
            if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
                $sql .= " AND p.fecha BETWEEN ? AND ?";
                $params[] = $filtros['fecha_inicio'];
                $params[] = $filtros['fecha_fin'];
            }
            if (isset($filtros['moneda'])) {
                $sql .= " AND p.moneda = ?";
                $params[] = $filtros['moneda'];
            }
            if (isset($filtros['id_metodopago'])) {
                $sql .= " AND p.id_metodopago = ?";
                $params[] = $filtros['id_metodopago'];
            }

            $sql .= " ORDER BY p.fecha DESC";
            
            $stmt = $con->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener pagos: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Obtiene el total de pagos por proyecto
     */
    public static function obtenerTotalPagosProyecto($idProyecto) {
        try {
            $con = Database::getConnection();
            $stmt = $con->prepare("CALL sp_obtener_total_pagos_proyecto(?)");
            $stmt->execute([$idProyecto]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener total de pagos por proyecto: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas generales de pagos
     */
    public static function obtenerEstadisticasPagos() {
        try {
            $con = Database::getConnection();
            $stmt = $con->prepare("CALL sp_obtener_estadisticas_pagos()");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de pagos: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Valida un pago específico
     */
    public static function validarPago($idPago) {
        try {
            $con = Database::getConnection();
            
            // Inicializar variables de salida
            $con->query("SET @p_es_valido = FALSE");
            $con->query("SET @p_mensaje = ''");
            
            // Ejecutar procedimiento
            $stmt = $con->prepare("CALL sp_validar_pago(?, @p_es_valido, @p_mensaje)");
            $stmt->execute([$idPago]);
            
            // Obtener resultados
            $resultado = $con->query("SELECT @p_es_valido as es_valido, @p_mensaje as mensaje")
                ->fetch(PDO::FETCH_ASSOC);
                
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al validar pago: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Cancela un pago
     */
    public static function cancelarPago($idPago, $motivo) {
        try {
            $con = Database::getConnection();
            $stmt = $con->prepare("CALL sp_cancelar_pago(?, ?)");
            $stmt->execute([$idPago, $motivo]);
            
            return [
                'idPago' => $idPago,
                'estado' => 'Cancelado',
                'motivo' => $motivo
            ];
        } catch (PDOException $e) {
            error_log("Error al cancelar pago: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Valida los datos del pago
     */
    private static function validarDatos($monto, $estado, $moneda, $idDonacion, $id_metodopago) {
        if (empty($monto) || $monto <= 0) {
            return false;
        }
        if (!in_array($estado, ['Pendiente', 'Completado', 'Cancelado'])) {
            return false;
        }
        if (!in_array($moneda, ['USD', 'EUR', 'MXN', 'HNL'])) {
            return false;
        }
        if (empty($idDonacion) || empty($id_metodopago)) {
            return false;
        }
        return true;
    }
}
?>
