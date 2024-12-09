<?php

namespace App\Modulos\Proyectos\Controladores;

use App\Modulos\Proyectos\Modelos\Proyecto;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use Exception;


class ProyectoController {
    /**
     * Obtiene las actividades
     */
    public function obtenerActividades(): void {
        try {
            $tipo = $_GET['tipo'] ?? null;
            
            $actividades = Proyecto::obtenerActividades($tipo);

            echo json_encode(ResponseHTTP::status200([
                "message" => "Actividades obtenidas exitosamente",
                "data" => $actividades
            ]));

        } catch (Exception $e) {
            error_log("Error en obtenerActividades: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Crea un nuevo proyecto
     */
    public function crearProyecto(): void {
        try {
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            $proyecto = Proyecto::crearProyecto(
                $data['titulo'],
                $data['descripcion'],
                $data['objetivo'],
                $data['meta'] ?? null,
                $data['tipo_actividad'],
                $tokenData->data->id
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Proyecto creado exitosamente",
                "data" => $proyecto
            ]));

        } catch (Exception $e) {
            error_log("Error en crearProyecto: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Actualiza un proyecto existente
     */
    public function actualizarProyecto(): void {
        try {
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            $proyecto = Proyecto::actualizarProyecto(
                $data['idProyecto'],
                $data['titulo'] ?? null,
                $data['descripcion'] ?? null,
                $data['objetivo'] ?? null,
                $data['meta'] ?? null,
                $data['estado'] ?? null,
                $data['tipo_actividad'] ?? null,
                $tokenData->data->id
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Proyecto actualizado exitosamente",
                "data" => $proyecto
            ]));

        } catch (Exception $e) {
            error_log("Error en actualizarProyecto: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Elimina un proyecto
     */
    public function eliminarProyecto(): void {
        try {
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $idProyecto = $_GET['id'] ?? null;
            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            Proyecto::eliminarProyecto($idProyecto, $tokenData->data->id);

            echo json_encode(ResponseHTTP::status200([
                "message" => "Proyecto eliminado exitosamente"
            ]));

        } catch (Exception $e) {
            error_log("Error en eliminarProyecto: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }
    /**
 /**
     * Cambia el estado de un proyecto
     */
    public function cambiarEstadoProyecto(): void {
        try {
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);

            $proyecto = Proyecto::cambiarEstadoProyecto(
                $data['idProyecto'],
                $data['estado'],
                $tokenData->data->id
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Estado del proyecto actualizado exitosamente",
                "data" => $proyecto
            ]));

        } catch (Exception $e) {
            error_log("Error en cambiarEstadoProyecto: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }
}
