<?php 

namespace App\Modulos\Recompensas\Controladores;

use App\Modulos\Recompensas\Modelos\Recompensa;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use Exception;

class recompensaController {
    /**
     * Obtiene los donadores estrella
     */
    public function obtenerDonadoresEstrella(): void {
        try {
            $resultado = Recompensa::obtenerDonadoresEstrellaGeneral();
            
            if ($resultado) {
                echo json_encode(ResponseHTTP::status200([
                    "message" => "Donadores estrella obtenidos exitosamente",
                    "data" => $resultado
                ]));
            } else {
                echo json_encode(ResponseHTTP::status200([
                    "message" => "No hay donadores estrella registrados",
                    "data" => []
                ]));
            }
        } catch (Exception $e) {
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Registra una nueva recompensa
     */
    public function registrarRecompensa(): void {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['descripcion']) || !isset($data['montoMinimo']) || 
                !isset($data['fechaEntregaEstimada']) || !isset($data['idProyecto'])) {
                echo json_encode(ResponseHTTP::status400("Faltan campos requeridos"));
                return;
            }

            $resultado = Recompensa::registrarRecompensa(
                $data['descripcion'],
                $data['montoMinimo'],
                $data['fechaEntregaEstimada'],
                $data['idProyecto']
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Recompensa registrada exitosamente",
                "data" => $resultado
            ]));
        } catch (Exception $e) {
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Aprueba o rechaza una recompensa
     */
    public function aprobarRecompensa(): void {
        try {
            // Validar token y rol de administrador
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->rol) || $tokenData->data->rol !== 'Administrador') {
                echo json_encode(ResponseHTTP::status403("No tienes permisos para aprobar recompensas"));
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['idRecompensa']) || !isset($data['aprobada'])) {
                echo json_encode(ResponseHTTP::status400("Faltan campos requeridos"));
                return;
            }

            $resultado = Recompensa::aprobarRecompensa(
                $data['idRecompensa'],
                $data['aprobada']
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Recompensa " . strtolower($data['aprobada']) . " exitosamente",
                "data" => $resultado
            ]));
        } catch (Exception $e) {
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }
}