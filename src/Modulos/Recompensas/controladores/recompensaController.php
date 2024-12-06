<?php

namespace App\Modulos\Recompensas\Controladores;

use App\Modulos\Recompensas\Modelos\recompensa;
use App\Configuracion\ResponseHTTP;

class recompensaController {
    public static function obtenerDonadoresEstrella() {
        try {
            $donadores = recompensa::obtenerDonadoresEstrella();
            echo json_encode(ResponseHTTP::status200($donadores));
        } catch (\Exception $e) {
            echo json_encode(ResponseHTTP::status500($e->getMessage()));
        }
    }

    public static function registrarRecompensa($data) {
        try {
            if (!isset($data['usuario_id'], $data['tipo_recompensa'])) {
                echo json_encode(ResponseHTTP::status400("Datos insuficientes"));
                return;
            }

            $exito = recompensa::registrarRecompensa($data['usuario_id'], $data['tipo_recompensa']);

            if ($exito) {
                echo json_encode(ResponseHTTP::status201("Recompensa registrada exitosamente"));
            } else {
                echo json_encode(ResponseHTTP::status400("Error al registrar recompensa"));
            }
        } catch (\Exception $e) {
            echo json_encode(ResponseHTTP::status500($e->getMessage()));
        }
    }

    public static function obtenerRecompensasPorUsuario($idUsuario) {
        try {
            $recompensas = recompensa::obtenerRecompensasPorUsuario($idUsuario);
            echo json_encode(ResponseHTTP::status200($recompensas));
        } catch (\Exception $e) {
            echo json_encode(ResponseHTTP::status500($e->getMessage()));
        }
    }

    public static function verificarElegibilidad($idUsuario) {
        try {
            $resultado = recompensa::verificarElegibilidad($idUsuario);
            echo json_encode(ResponseHTTP::status200($resultado));
        } catch (\Exception $e) {
            echo json_encode(ResponseHTTP::status500($e->getMessage()));
        }
    }
}
