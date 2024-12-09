<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

use App\Modulos\Usuarios\Controladores\UsuarioController;
use App\Configuracion\ResponseHTTP;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

$usuarioController = new UsuarioController();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode($usuarioController->registrar());
    } else {
        echo json_encode(ResponseHTTP::status405('Método no permitido'));
    }
} catch (Exception $e) {
    error_log("Error en ruta de registro: " . $e->getMessage());
    echo json_encode(ResponseHTTP::status500("Error interno del servidor"));
}
