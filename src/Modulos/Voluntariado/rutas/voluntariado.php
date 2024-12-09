<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

use App\Modulos\Voluntariado\Controladores\VoluntariadoController;
use App\Configuracion\ResponseHTTP;

// Para peticiones OPTIONS (pre-flight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

try {
    // Crear instancia del controlador
    $voluntariadoController = new VoluntariadoController();

    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    // Obtener la ruta específica (si existe)
    $rutaEspecifica = isset($url[1]) ? $url[1] : '';

    // Manejar las rutas
    switch ($method) {
        case 'GET':
            if ($rutaEspecifica === 'listar') {
                $voluntariadoController->listarVoluntarios();
            } else if ($rutaEspecifica === 'verificar') {
                $voluntariadoController->verificarVinculacion();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        case 'POST':
            if ($rutaEspecifica === 'vincular') {
                $voluntariadoController->vincular();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        case 'DELETE':
            if ($rutaEspecifica === 'eliminar') {
                $voluntariadoController->eliminar();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        default:
            echo json_encode(ResponseHTTP::status400("Método no permitido"));
    }
} catch (Exception $e) {
    error_log("Error en rutas de voluntariado: " . $e->getMessage());
    echo json_encode(ResponseHTTP::status500("Error interno del servidor"));
}
?>
