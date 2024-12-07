<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use App\Modulos\Usuarios\Controladores\GetAllUsuariosController;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $controller = new GetAllUsuariosController();
        $usuarios = $controller->getAllUsuarios();
        
        echo json_encode([
            'status' => 200,
            'message' => 'Usuarios obtenidos exitosamente',
            'data' => $usuarios
        ]);
    } catch (Exception $e) {
        error_log("Error en GetAllUsuarios: " . $e->getMessage());
        echo json_encode([
            'status' => 500,
            'message' => 'Error interno del servidor'
        ]);
    }
} else {
    echo json_encode([
        'status' => 405,
        'message' => 'Método no permitido'
    ]);
}
