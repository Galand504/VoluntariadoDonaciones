<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use App\Controladores\LoginController;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Solo responder con un código 200 a las solicitudes OPTIONS
    header('HTTP/1.1 200 OK');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    exit;
}
// Verificar que el método sea POST
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

    // Obtener el correo y la contraseña de la solicitud
    $email = $data['email'];
    $contraseña = $data['contraseña'];

    // Instanciar el controlador de login
    $controller = new LoginController();
    $response = $controller->login($email, $contraseña);

    // Devolver la respuesta al cliente
    echo $response;

} else {
    // Método no permitido
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);
}
