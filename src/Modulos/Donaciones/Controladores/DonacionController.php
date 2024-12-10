<?php

namespace App\Modulos\Donaciones\Controladores;

use App\Modulos\Donaciones\Modelos\Donacion;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use Exception;

class DonacionController
{
    public function obtenerDonaciones(): void
    {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            // Obtener donaciones
            $donaciones = Donacion::obtenerDonacionesConProgreso();
            
            echo json_encode(ResponseHTTP::status200([
                "message" => "Donaciones obtenidas exitosamente",
                "data" => $donaciones
            ]));

        } catch (Exception $e) {
            error_log("Error en DonacionController::obtenerDonaciones - " . $e->getMessage());
            echo json_encode(ResponseHTTP::status500($e->getMessage()));
        }
    }
    /**
     * Vincula un usuario a un proyecto de donación
     */
    public function vincular(): void
    {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            // Obtener datos
            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['idProyecto'])) {
                echo json_encode(ResponseHTTP::status400("ID de proyecto requerido"));
                return;
            }

            // Obtener ID de usuario del token
            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            // Verificar si ya existe una vinculación
            if (Donacion::existeVinculacion($tokenData->data->id, $data['idProyecto'])) {
                echo json_encode(ResponseHTTP::status400("Ya existe una vinculación para este proyecto"));
                return;
            }

            // Vincular usuario con proyecto
            $idDonacion = Donacion::vincular(
                $tokenData->data->id,
                $data['idProyecto']
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Usuario vinculado exitosamente al proyecto",
                "data" => [
                    "idDonacion" => $idDonacion
                ]
            ]));

        } catch (Exception $e) {
            error_log("Error en DonacionController::vincular - " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Verifica si existe una vinculación
     */
    public function verificarVinculacion(): void
    {
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

            $existe = Donacion::existeVinculacion(
                $tokenData->data->id,
                $idProyecto
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Verificación exitosa",
                "data" => [
                    "existe" => $existe
                ]
            ]));

        } catch (Exception $e) {
            error_log("Error en DonacionController::verificarVinculacion - " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }
}
