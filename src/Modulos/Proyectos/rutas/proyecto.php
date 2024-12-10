<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

use App\Modulos\Proyectos\Controladores\ProyectoController;
use App\Configuracion\ResponseHTTP;

// Para peticiones OPTIONS (pre-flight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

try {
    // Crear instancia del controlador
    $proyectoController = new ProyectoController();

    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    // Obtener la ruta específica y el ID si existe
    $rutaEspecifica = isset($url[1]) ? $url[1] : '';
    $id = isset($url[2]) ? $url[2] : null;

    // Manejar las rutas
    switch ($method) {
        case 'GET':
            if ($rutaEspecifica === 'actividades') {
                $proyectoController->obtenerActividades();
            } elseif ($rutaEspecifica === 'obtener' && $id) {
                $_GET['id'] = $id; // Asignar el ID a $_GET
                $proyectoController->obtenerProyecto();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada o ID no proporcionado"));
            }
            break;

        case 'POST':
            if ($rutaEspecifica === 'crear') {
                $proyectoController->crearProyecto();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        case 'PUT':
            if ($rutaEspecifica === 'actualizar') {
                $proyectoController->actualizarProyecto();
            } elseif ($rutaEspecifica === 'cambiar-estado') {
                $proyectoController->cambiarEstadoProyecto();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;
        case 'DELETE':
            if ($rutaEspecifica === 'eliminar') {
                $proyectoController->eliminarProyecto();
            } else {
                echo json_encode(ResponseHTTP::status400("Ruta no encontrada"));
            }
            break;

        default:
            echo json_encode(ResponseHTTP::status400("Método no permitido"));
    }
} catch (Exception $e) {
    error_log("Error en rutas de proyecto: " . $e->getMessage());
    echo json_encode(ResponseHTTP::status500("Error interno del servidor"));
}
