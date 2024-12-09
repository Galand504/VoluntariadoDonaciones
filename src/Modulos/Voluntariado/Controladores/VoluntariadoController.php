<?php

namespace App\Modulos\Voluntariado\Controladores;

use App\Modulos\Voluntariado\Modelos\Voluntariado;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use Exception;

class VoluntariadoController {
    
    /**
     * Vincula un usuario como voluntario a un proyecto
     */
    public function vincular(): void {
        try {
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['idProyecto']) || !isset($data['disponibilidad'])) {
                echo json_encode(ResponseHTTP::status400("Datos incompletos"));
                return;
            }

            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            $idVoluntario = Voluntariado::vincular(
                $tokenData->data->id,
                $data['idProyecto'],
                $data['disponibilidad']
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Registrado exitosamente como voluntario",
                "data" => ["idVoluntario" => $idVoluntario]
            ]));

        } catch (Exception $e) {
            error_log("Error en VoluntariadoController::vincular - " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Lista los voluntarios de un proyecto
     * Solo accesible para el organizador del proyecto
     */
    public function listarVoluntarios(): void {
        try {
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $idProyecto = $_GET['idProyecto'] ?? null;
            if (!$idProyecto) {
                echo json_encode(ResponseHTTP::status400("ID de proyecto requerido"));
                return;
            }

            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            $voluntarios = Voluntariado::listarVoluntarios(
                $idProyecto,
                $tokenData->data->id
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Voluntarios obtenidos exitosamente",
                "data" => $voluntarios
            ]));

        } catch (Exception $e) {
            error_log("Error en VoluntariadoController::listarVoluntarios - " . $e->getMessage());
            
            if (strpos($e->getMessage(), "No tienes permisos") !== false) {
                echo json_encode(ResponseHTTP::status403($e->getMessage()));
                return;
            }
            
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Elimina un voluntario del proyecto
     * Solo el organizador puede eliminar voluntarios
     */
    public function eliminar(): void {
        try {
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $idVoluntario = $_GET['id'] ?? null;
            if (!$idVoluntario) {
                echo json_encode(ResponseHTTP::status400("ID de voluntario requerido"));
                return;
            }

            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            Voluntariado::eliminar(
                $idVoluntario,
                $tokenData->data->id
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Voluntario eliminado exitosamente"
            ]));

        } catch (Exception $e) {
            error_log("Error en VoluntariadoController::eliminar - " . $e->getMessage());
            
            if (strpos($e->getMessage(), "No tienes permisos") !== false) {
                echo json_encode(ResponseHTTP::status403($e->getMessage()));
                return;
            }
            
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Verifica si un usuario está vinculado a un proyecto
     */
    public function verificarVinculacion(): void {
        try {
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $idProyecto = $_GET['idProyecto'] ?? null;
            if (!$idProyecto) {
                echo json_encode(ResponseHTTP::status400("ID de proyecto requerido"));
                return;
            }

            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            $existe = Voluntariado::existeVinculacion(
                $tokenData->data->id,
                $idProyecto
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Verificación exitosa",
                "data" => ["existe" => $existe]
            ]));

        } catch (Exception $e) {
            error_log("Error en VoluntariadoController::verificarVinculacion - " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }
}
