<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir el controlador
use App\Modulos\Usuarios\Controladores\AddUsuarioController;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Solo responder con un código 200 a las solicitudes OPTIONS
    header('HTTP/1.1 200 OK');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    exit;
    if (!$data) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se enviaron datos.'
        ]);
        exit;
    }
}

// Verificar que el método sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados en el cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    // Validar si hay datos
    if (!$data) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se enviaron datos.'
        ]);
        exit;
    }

    // Instanciar el controlador y procesar la solicitud
    $controller = new AddUsuarioController();
    $response = $controller->addUsuario($data);

    // Retornar la respuesta al cliente
    echo $response;
} else {
    // Método no permitido
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);
}
