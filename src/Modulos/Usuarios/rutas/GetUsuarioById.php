<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Manejar preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir el controlador
use App\Modulos\Usuarios\Controladores\GetUsuarioByIdController;

// Obtener el ID del usuario de la URL
$idUsuario = isset($_GET['id']) ? $_GET['id'] : null;

// Si no hay ID en la URL, intentar obtenerlo del cuerpo
if (!$idUsuario) {
    $data = json_decode(file_get_contents("php://input"), true);
    $idUsuario = $data['id_usuario'] ?? null;
}

if ($idUsuario) {
    // Instanciar el controlador y procesar la solicitud
    $controller = new GetUsuarioByIdController();
    $response = $controller->getUsuarioById($idUsuario);
    echo json_encode($response);
} else {
    // Manejar errores
    echo json_encode([
        'status' => 'error',
        'message' => 'Falta el ID del usuario.'
    ]);
}
