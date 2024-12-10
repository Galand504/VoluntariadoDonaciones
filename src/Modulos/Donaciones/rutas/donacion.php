<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

use App\Modulos\Donaciones\Controladores\DonacionController;
use App\Configuracion\ResponseHTTP;

// Para peticiones OPTIONS (pre-flight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

try {
    // Crear instancia del controlador
    $donacionController = new DonacionController();

    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    // Obtener la ruta específica (si existe)
    $rutaEspecifica = isset($url[1]) ? $url[1] : '';

    // Manejar las rutas
    switch ($method) {
        case 'GET':
            if ($rutaEspecifica === 'obtener') {
                $donacionController->obtenerDonaciones();
            } else if ($rutaEspecifica === 'verificar') {
                $donacionController->verificarVinculacion();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        case 'POST':
            if ($rutaEspecifica === 'vincular') {
                $donacionController->vincular();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        default:
            echo json_encode(ResponseHTTP::status400("Método no permitido"));
    }
} catch (Exception $e) {
    error_log("Error en rutas de donación: " . $e->getMessage());
    echo json_encode(ResponseHTTP::status500("Error interno del servidor"));
}
?>

