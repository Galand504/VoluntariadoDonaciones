<?php 

namespace App\Modulos\Recompensas\Controladores;

use App\Modulos\Recompensas\Modelos\recompensa;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use Exception;

class recompensaController {
    /**
     * Obtiene los donadores estrella
     */
    public function obtenerDonadoresEstrella(): void {
        try {
            // Verificar si se especifica un proyecto
            $idProyecto = $_GET['proyecto'] ?? null;
            
            if ($idProyecto) {
                $resultado = Recompensa::obtenerDonadoresEstrellaPorProyecto($idProyecto);
            } else {
                $resultado = Recompensa::obtenerDonadoresEstrellaGeneral();
            }
            
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
            error_log("Error en obtenerDonadoresEstrella: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Registra una nueva recompensa
     */
    public function registrarRecompensa(): void {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            // Obtener datos del POST
            $data = json_decode(file_get_contents("php://input"), true);

            // Validar campos requeridos
            $camposRequeridos = ['descripcion', 'montoMinimo', 'moneda', 'fechaEntregaEstimada', 'idProyecto'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($data[$campo]) || empty($data[$campo])) {
                    echo json_encode(ResponseHTTP::status400("El campo $campo es requerido"));
                    return;
                }
            }

            // Validar monto mínimo
            if (!is_numeric($data['montoMinimo']) || $data['montoMinimo'] <= 0) {
                echo json_encode(ResponseHTTP::status400("El monto mínimo debe ser un número mayor a 0"));
                return;
            }

            // Validar moneda
            $monedasValidas = ['HNL', 'USD', 'EUR', 'MXN'];
            if (!in_array(strtoupper($data['moneda']), $monedasValidas)) {
                echo json_encode(ResponseHTTP::status400("Moneda inválida. Monedas permitidas: HNL, USD, EUR, MXN"));
                return;
            }

            // Validar fecha
            if (!strtotime($data['fechaEntregaEstimada'])) {
                echo json_encode(ResponseHTTP::status400("Fecha de entrega inválida"));
                return;
            }

            // Registrar recompensa
            $recompensa = Recompensa::registrarRecompensa(
                $data['descripcion'],
                $data['montoMinimo'],
                strtoupper($data['moneda']), // Convertir a mayúsculas
                $data['fechaEntregaEstimada'],
                $data['idProyecto']
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Recompensa registrada exitosamente",
                "data" => $recompensa
            ]));

        } catch (Exception $e) {
            error_log("Error en registrarRecompensa: " . $e->getMessage());
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

    /**
     * Actualiza el estado de entrega de una recompensa
     */
    public function actualizarEstadoEntrega(): void {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            // Validar rol de administrador y obtener ID
            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->rol) || $tokenData->data->rol !== 'Administrador') {
                echo json_encode(ResponseHTTP::status403("No tienes permisos para actualizar estados de recompensas"));
                return;
            }

            $idAdmin = $tokenData->data->id;

            // Obtener datos del POST
            $data = json_decode(file_get_contents("php://input"), true);

            // Validar campos requeridos
            $camposRequeridos = ['idRecompensa', 'idUsuario', 'estadoEntrega'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($data[$campo])) {
                    echo json_encode(ResponseHTTP::status400("El campo $campo es requerido"));
                    return;
                }
            }

            // Validar estado de entrega
            $estadosValidos = ['Pendiente', 'En Proceso', 'Entregado'];
            if (!in_array($data['estadoEntrega'], $estadosValidos)) {
                echo json_encode(ResponseHTTP::status400("Estado de entrega inválido. Estados permitidos: Pendiente, En Proceso, Entregado"));
                return;
            }

            // Actualizar estado
            $resultado = Recompensa::actualizarEstadoEntrega(
                $data['idRecompensa'],
                $data['idUsuario'],
                $data['estadoEntrega'],
                $idAdmin
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Estado de recompensa actualizado exitosamente",
                "data" => $resultado
            ]));

        } catch (Exception $e) {
            error_log("Error en actualizarEstadoEntrega: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Obtiene las recompensas asignadas
     */
    public function obtenerRecompensasAsignadas(): void {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            // Obtener filtros del GET
            $filtros = [
                'estadoEntrega' => $_GET['estado'] ?? null,
                'idUsuario' => $_GET['usuario'] ?? null,
                'idProyecto' => $_GET['proyecto'] ?? null
            ];

            $recompensas = Recompensa::obtenerRecompensasAsignadas($filtros);

            echo json_encode(ResponseHTTP::status200([
                "message" => "Recompensas obtenidas exitosamente",
                "data" => $recompensas
            ]));

        } catch (Exception $e) {
            error_log("Error en obtenerRecompensasAsignadas: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }
    public function obtenerTodasRecompensas() {
        try {
            $recompensas = Recompensa::obtenerTodasRecompensas();
            echo json_encode(ResponseHTTP::status200($recompensas));
        } catch (Exception $e) {
            echo json_encode(ResponseHTTP::status500($e->getMessage()));
        }
    }
    public function obtenerRecompensaPorId() {
        try {
            $idRecompensa = $_GET['id'] ?? null;
            
            if (!$idRecompensa) {
                echo json_encode(ResponseHTTP::status400("ID de recompensa no proporcionado"));
                return;
            }
    
            $recompensa = Recompensa::obtenerRecompensaPorId($idRecompensa);
    
            if (!$recompensa) {
                echo json_encode(ResponseHTTP::status404("Recompensa no encontrada"));
                return;
            }
    
            echo json_encode(ResponseHTTP::status200($recompensa));
            
        } catch (Exception $e) {
            echo json_encode(ResponseHTTP::status500($e->getMessage()));
        }
    }
}
