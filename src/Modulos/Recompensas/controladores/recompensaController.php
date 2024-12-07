<?php 

namespace App\Modulos\Recompensas\Controladores;

use App\Modulos\Recompensas\Modelos\recompensa;
use App\Configuracion\responseHTTP;

class recompensaController {
    public static function obtenerDonadoresEstrella() {
        try {
            $donadores = recompensa::obtenerDonadoresEstrella();
            echo json_encode(responseHTTP::status200($donadores));
        } catch (\Exception $e) {
            echo json_encode(responseHTTP::status500($e->getMessage()));
        }
    }
    public static function registrarRecompensa($data) {
        try {
            // Validación básica: Datos mínimos requeridos
            if (!isset($data['usuario_id'], $data['tipo_recompensa'])) {
                echo json_encode(responseHTTP::status400("Datos insuficientes"));
                return;
            }
    
            // Validación específica: usuario_id debe ser un número y tipo_recompensa no debe estar vacío
            if (!is_numeric($data['usuario_id']) || empty(trim($data['tipo_recompensa']))) {
                echo json_encode(responseHTTP::status400("Datos inválidos"));
                return;
            }
    
            // Intentar registrar la recompensa en el modelo
            $exito = recompensa::registrarRecompensa($data['usuario_id'], $data['tipo_recompensa']);
    
            // Respuesta según el resultado
            if ($exito) {
                echo json_encode(responseHTTP::status201("Recompensa registrada exitosamente"));
            } else {
                echo json_encode(responseHTTP::status400("Error al registrar recompensa"));
            }
        } catch (\Exception $e) {
            // Manejo de errores internos
            echo json_encode(responseHTTP::status500($e->getMessage()));
        }
    }
    
    public static function obtenerRecompensasPorUsuario($idUsuario) {
        try {
            $recompensas = recompensa::obtenerRecompensasPorUsuario($idUsuario);
    
            if (empty($recompensas)) {
                $respuesta = json_encode([
                    'message' => 'No se encontraron recompensas para el usuario'
                ]);
                echo $respuesta;
                return;
            }
    
            $respuesta = json_encode([
                'message' => 'Recompensas obtenidas exitosamente',
                'data' => $recompensas
            ]);
            echo $respuesta;
        } catch (\Exception $e) {
            // Manejo de errores internos
            error_log("Error en obtenerRecompensasPorUsuario: " . $e->getMessage()); // Registrar en el log
            $respuesta = json_encode([
                'message' => 'Error interno del servidor'
            ]);
            echo $respuesta;
        }
    }

    public static function verificarElegibilidad($idUsuario) {
        try {
            $resultado = recompensa::verificarElegibilidad($idUsuario);
            echo json_encode(responseHTTP::status200($resultado));
        } catch (\Exception $e) {
            echo json_encode(responseHTTP::status500($e->getMessage()));
        }
    }
}

