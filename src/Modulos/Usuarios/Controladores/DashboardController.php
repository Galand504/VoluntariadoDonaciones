<?php

namespace App\Modulos\Usuarios\Controladores;

use App\Modulos\Usuarios\Modelos\ConteoyFechas;
use Exception;

class DashboardController
{
    public function getDashboardCounts()
    {
        try {
            $result = ConteoyFechas::getDashboardCounts();
            return json_encode($result);
        } catch (Exception $e) {
            error_log("Error en DashboardController::getDashboardCounts: " . $e->getMessage());
            return json_encode([
                'status' => false,
                'message' => 'Error al obtener los conteos'
            ]);
        }
    }

    public function getRegistrosPorFecha()
    {
        try {
            $result = ConteoyFechas::getRegistrosPorFecha();
            return json_encode($result);
        } catch (Exception $e) {
            error_log("Error en DashboardController::getRegistrosPorFecha: " . $e->getMessage());
            return json_encode([
                'status' => false,
                'message' => 'Error al obtener los registros por fecha'
            ]);
        }
    }

    public function getActividades()
    {
        try {
            $result = ConteoyFechas::getActividades();
            return json_encode($result);
        } catch (Exception $e) {
            error_log("Error en DashboardController::getActividades: " . $e->getMessage());
            return json_encode([
                'status' => false,
                'message' => 'Error al obtener las actividades'
            ]);
        }
    }
} 