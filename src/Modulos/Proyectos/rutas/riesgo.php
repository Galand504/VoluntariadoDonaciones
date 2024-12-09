<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

use App\Modulos\Riesgo\Controladores\RiesgoController;
use App\Configuracion\responseHTTP;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

$riesgoController = new RiesgoController();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($riesgoController->registrar($data));
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($riesgoController->actualizar($data));
            break;

        case 'DELETE':
            $idRiesgo = $_GET['id'] ?? null;
            echo json_encode($riesgoController->eliminar($idRiesgo));
            break;

        case 'GET':
            $idProyecto = $_GET['idProyecto'] ?? null;
            echo json_encode($riesgoController->listarPorProyecto($idProyecto));
            break;

        default:
            echo json_encode(ResponseHTTP::status405('Método no permitido'));
            break;
    }
} catch (Exception $e) {
    error_log("Error en rutas de riesgo: " . $e->getMessage());
    echo json_encode(ResponseHTTP::status500("Error interno del servidor"));
}
