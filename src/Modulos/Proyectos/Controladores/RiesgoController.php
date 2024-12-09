<?php

namespace App\Modulos\Riesgo\Controladores;

use App\Modulos\Riesgo\Modelos\Riesgo;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use Exception;

class RiesgoController {
    
    /**
     * Registra un nuevo riesgo
     */
    public function registrar($data) {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                return ResponseHTTP::status401("Token inválido");
            }

            // Obtener id_usuario del token
            $headers = apache_request_headers();
            if (!isset($headers['Authorization'])) {
                return ResponseHTTP::status401("Token no proporcionado");
            }

            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->id)) {
                return ResponseHTTP::status400("No se pudo obtener la información del usuario");
            }

            // Validar datos requeridos
            if (!isset($data['descripcion']) || !isset($data['planMitigacion']) || 
                !isset($data['idProyecto'])) {
                return ResponseHTTP::status400('Faltan datos requeridos');
            }

            $riesgo = Riesgo::registrar(
                $data['descripcion'],
                $data['planMitigacion'],
                $data['idProyecto'],
                $tokenData->data->id
            );

            return ResponseHTTP::status200('Riesgo registrado exitosamente');
            
        } catch (Exception $e) {
            error_log("Error en RiesgoController::registrar - " . $e->getMessage());
            return ResponseHTTP::status500($e->getMessage());
        }
    }

    /**
     * Actualiza un riesgo existente
     */
    public function actualizar($data) {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                return ResponseHTTP::status401("Token inválido");
            }

            // Obtener id_usuario del token
            $headers = apache_request_headers();
            if (!isset($headers['Authorization'])) {
                return ResponseHTTP::status401("Token no proporcionado");
            }

            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->id)) {
                return ResponseHTTP::status400("No se pudo obtener la información del usuario");
            }

            // Validar datos requeridos
            if (!isset($data['idRiesgo']) || !isset($data['descripcion']) || 
                !isset($data['planMitigacion'])) {
                return ResponseHTTP::status400('Faltan datos requeridos');
            }

            $riesgo = Riesgo::actualizar(
                $data['idRiesgo'],
                $data['descripcion'],
                $data['planMitigacion'],
                $tokenData->data->id
            );

            return ResponseHTTP::status200('Riesgo actualizado exitosamente');
            
        } catch (Exception $e) {
            error_log("Error en RiesgoController::actualizar - " . $e->getMessage());
            return ResponseHTTP::status500($e->getMessage());
        }
    }

    /**
     * Elimina un riesgo
     */
    public function eliminar($idRiesgo) {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                return ResponseHTTP::status401("Token inválido");
            }

            // Obtener id_usuario del token
            $headers = apache_request_headers();
            if (!isset($headers['Authorization'])) {
                return ResponseHTTP::status401("Token no proporcionado");
            }

            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->id)) {
                return ResponseHTTP::status400("No se pudo obtener la información del usuario");
            }

            if (!$idRiesgo) {
                return ResponseHTTP::status400('ID de riesgo requerido');
            }

            $resultado = Riesgo::eliminar($idRiesgo, $tokenData->data->id);
            return ResponseHTTP::status200('Riesgo eliminado exitosamente');
            
        } catch (Exception $e) {
            error_log("Error en RiesgoController::eliminar - " . $e->getMessage());
            return ResponseHTTP::status500($e->getMessage());
        }
    }

    /**
     * Lista los riesgos de un proyecto
     */
    public function listarPorProyecto($idProyecto) {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                return ResponseHTTP::status401("Token inválido");
            }

            // Obtener id_usuario del token
            $headers = apache_request_headers();
            if (!isset($headers['Authorization'])) {
                return ResponseHTTP::status401("Token no proporcionado");
            }

            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->id)) {
                return ResponseHTTP::status400("No se pudo obtener la información del usuario");
            }

            if (!$idProyecto) {
                return ResponseHTTP::status400('ID de proyecto requerido');
            }

            $riesgos = Riesgo::listarPorProyecto($idProyecto);
            return ResponseHTTP::status200('Riesgos obtenidos exitosamente');
            
        } catch (Exception $e) {
            error_log("Error en RiesgoController::listarPorProyecto - " . $e->getMessage());
            return ResponseHTTP::status500($e->getMessage());
        }
    }
}
