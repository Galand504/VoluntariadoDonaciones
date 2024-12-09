<?php 

namespace App\Modulos\Donaciones\Controladores;

use App\Modulos\Donaciones\Modelos\Pago;
use App\Configuracion\ResponseHTTP;
use App\Configuracion\Security;
use Exception;

class Pagocontroller {

    /**
     * Crea un nuevo pago
     */
    public function crearPago(): void {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            // Obtener id_usuario del token
            $headers = apache_request_headers();
            if (!isset($headers['Authorization'])) {
                echo json_encode(ResponseHTTP::status401("Token no proporcionado"));
                return;
            }

            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->id)) {
                echo json_encode(ResponseHTTP::status400("No se pudo obtener la información del usuario"));
                return;
            }

            $idUsuario = $tokenData->data->id;

            // Obtener datos del POST
            $data = json_decode(file_get_contents("php://input"), true);

            // Validar campos requeridos
            $camposRequeridos = ['monto', 'idMetodoPago', 'moneda', 'idDonacion'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($data[$campo])) {
                    echo json_encode(ResponseHTTP::status400("El campo $campo es requerido"));
                    return;
                }
            }

            // Verificar que la donación pertenece al usuario

            // Crear el pago
            $pago = Pago::crearPago(
                $data['monto'],
                'Pendiente',
                $data['idDonacion'],
                $data['idMetodoPago'],
                $data['moneda'],
                $this->generarReferencia($data['idMetodoPago'], $data['monto'], $data['moneda'])
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Pago registrado exitosamente",
                "data" => $pago
            ]));

        } catch (Exception $e) {
            error_log("Error en crearPago: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Obtiene lista de pagos con filtros
     */
    public function obtenerPagos(): void {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $filtros = [
                'estado' => $_GET['estado'] ?? null,
                'idProyecto' => $_GET['proyecto'] ?? null,
                'id_usuario' => $_GET['usuario'] ?? null,
                'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
                'fecha_fin' => $_GET['fecha_fin'] ?? null,
                'moneda' => $_GET['moneda'] ?? null,
                'id_metodopago' => $_GET['metodo_pago'] ?? null
            ];

            // Eliminar filtros nulos
            $filtros = array_filter($filtros);

            $pagos = Pago::obtenerPagos($filtros);

            echo json_encode(ResponseHTTP::status200([
                "message" => "Pagos obtenidos exitosamente",
                "data" => $pagos
            ]));

        } catch (Exception $e) {
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Actualiza el estado de un pago
     */
    public function actualizarEstadoPago(): void {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            // Validar rol de administrador
            $headers = apache_request_headers();
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $tokenData = Security::getTokenData($token);
            
            if (!isset($tokenData->data->rol) || $tokenData->data->rol !== 'Administrador') {
                echo json_encode(ResponseHTTP::status403("No tienes permisos para actualizar estados de pago"));
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['idPago']) || !isset($data['estado'])) {
                echo json_encode(ResponseHTTP::status400("Faltan campos requeridos"));
                return;
            }

            $resultado = Pago::actualizarEstado($data['idPago'], $data['estado']);

            echo json_encode(ResponseHTTP::status200([
                "message" => "Estado actualizado exitosamente",
                "data" => $resultado
            ]));

        } catch (Exception $e) {
            error_log("Error en actualizarEstadoPago: " . $e->getMessage());
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Cancela un pago
     */
    public function cancelarPago(): void {
        try {
            // Validar token
            if (!Security::validateTokenJwt(Security::secretKey())) {
                echo json_encode(ResponseHTTP::status401("Token inválido"));
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['idPago'])) {
                echo json_encode(ResponseHTTP::status400("ID de pago requerido"));
                return;
            }

            $resultado = Pago::cancelarPago(
                $data['idPago'],
                $data['motivo'] ?? 'Cancelación solicitada'
            );

            echo json_encode(ResponseHTTP::status200([
                "message" => "Pago cancelado exitosamente",
                "data" => $resultado
            ]));

        } catch (Exception $e) {
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    /**
     * Obtiene estadísticas de pagos
     */
    public function obtenerEstadisticas(): void {
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
                echo json_encode(ResponseHTTP::status403("No tienes permisos para ver estadísticas"));
                return;
            }

            $estadisticas = Pago::obtenerEstadisticasPagos();

            echo json_encode(ResponseHTTP::status200([
                "message" => "Estadísticas obtenidas exitosamente",
                "data" => $estadisticas
            ]));

        } catch (Exception $e) {
            echo json_encode(ResponseHTTP::status400($e->getMessage()));
        }
    }

    private function generarReferencia($idMetodoPago, $monto, $moneda): string {
        $prefijos = [
            1 => 'CRED',  // Tarjeta de Crédito
            2 => 'DEBT',  // Tarjeta de Débito
            3 => 'PYPL',  // PayPal
            4 => 'BANK'   // Transferencia Bancaria
        ];

        $prefijo = $prefijos[$idMetodoPago] ?? 'PAY';
        $fecha = date('Ymd');
        $hora = date('His');
        $montoFormateado = str_pad(number_format($monto, 2, '', ''), 10, '0', STR_PAD_LEFT);
        
        return sprintf(
            '%s-%s-%s-%s-%s',
            $prefijo,
            $fecha,
            $hora,
            strtoupper($moneda),
            $montoFormateado
        );
    }
}



