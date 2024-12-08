<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

use App\Modulos\Donaciones\Controladores\PagoController;
use App\Configuracion\ResponseHTTP;

// Para peticiones OPTIONS (pre-flight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

try {
    // Crear instancia del controlador
    $pagoController = new PagoController();

    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    // Obtener la ruta específica (si existe)
    $rutaEspecifica = isset($url[1]) ? $url[1] : '';

    // Manejar las rutas
    switch ($method) {
        case 'GET':
            if ($rutaEspecifica === 'estadisticas') {
                $pagoController->obtenerEstadisticas();
            } elseif (empty($rutaEspecifica)) {
                $pagoController->obtenerPagos();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        case 'POST':
            switch ($rutaEspecifica) {
                case 'crear':
                    $pagoController->crearPago();
                    break;
                case 'cancelar':
                    $pagoController->cancelarPago();
                    break;
                case 'actualizar-estado':
                    $pagoController->actualizarEstadoPago();
                    break;
                default:
                    echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        default:
            echo json_encode(ResponseHTTP::status400("Método no permitido"));
    }
} catch (Exception $e) {
    error_log("Error en rutas de pago: " . $e->getMessage());
    echo json_encode(ResponseHTTP::status500("Error interno del servidor"));
}

/**
 * API Endpoints:
 * 
 * POST /pagos/crear
 * Body: {
 *   "monto": float,
 *   "estado": string,
 *   "idDonacion": int,
 *   "id_metodopago": int,
 *   "moneda": string,
 *   "referencia_externa": string (opcional)
 * }
 * 
 * POST /pagos/cancelar
 * Body: {
 *   "idPago": int,
 *   "motivo": string (opcional)
 * }
 * 
 * POST /pagos/actualizar-estado
 * Body: {
 *   "idPago": int,
 *   "estado": string
 * }
 * 
 * GET /pagos
 * Query params opcionales:
 *   - estado
 *   - proyecto
 *   - usuario
 *   - fecha_inicio
 *   - fecha_fin
 *   - moneda
 *   - metodo_pago
 * 
 * GET /pagos/estadisticas
 * Requiere rol de Administrador
 */
?>

