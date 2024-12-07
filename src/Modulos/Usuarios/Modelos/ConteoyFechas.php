<?php
namespace App\Modulos\Usuarios\Modelos;

use App\Base\Database;
use PDO;
use PDOException;

class ConteoyFechas {
    public static function getDashboardCounts()
    {
        try {
            $conexion = Database::getConnection();
            
            $query = "CALL sp_get_dashboard_counts()";
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'status' => 200,
                'personas' => $result['personas'],
                'empresas' => $result['empresas'],
                'voluntariados' => $result['voluntariados'],
                'donaciones' => $result['donaciones']
            ];

        } catch (PDOException $e) {
            error_log("Error en usuario::getDashboardCounts: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error al obtener los conteos'
            ];
        }
    }

    public static function getRegistrosPorFecha()
    {
        try {
            $conexion = Database::getConnection();
            
            $query = "CALL sp_get_registros_por_fecha()";
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'status' => 200,
                    'fechas' => array_column($result, 'fecha'),
                    'conteos' => array_column($result, 'total')
                ];
            }

            return [
                'status' => false,
                'message' => 'No se encontraron registros'
            ];

        } catch (PDOException $e) {
            error_log("Error en ConteoyFechas::getRegistrosPorFecha: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error al obtener los registros por fecha'
            ];
        }
    }

    public static function getActividades()
    {
        try {
            $conexion = Database::getConnection();
            
            $query = "CALL sp_get_actividades()";
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'status' => 200,
                    'actividades' => $result
                ];
            }

            return [
                'status' => false,
                'message' => 'No se encontraron actividades'
            ];

        } catch (PDOException $e) {
            error_log("Error en ConteoyFechas::getActividades: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error al obtener las actividades'
            ];
        }
    }
}     