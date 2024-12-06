<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use App\Modulos\Usuarios\Controladores\LoginController;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados en el cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar que se haya enviado el correo y la contraseña
    if (empty($data['email']) || empty($data['contraseña'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'El correo y la contraseña son requeridos.'
        ]);
        exit;
    }

    // Instanciar el controlador de login y pasar el array completo
    $controller = new LoginController();
    $response = $controller->login($data['email'], $data['contraseña']);

    // Devolver la respuesta al cliente
    echo json_encode($response);

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);
}
