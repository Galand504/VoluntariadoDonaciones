<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir el controlador
use App\Modulos\Usuarios\Controladores\DashboardController;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Solo responder con un código 200 a las solicitudes OPTIONS
    header('HTTP/1.1 200 OK');
    exit;
}

// Verificar que el método sea GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Instanciar el controlador y procesar la solicitud
    $controller = new DashboardController();
    $response = $controller->getActividades();

    // Retornar la respuesta al cliente
    echo $response;
} else {
    // Método no permitido
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);
}
