<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Incluir el controlador
use App\Controladores\GetUsuarioByIdController;

// Obtener el ID del usuario de la URL o el cuerpo
$data = json_decode(file_get_contents("php://input"), true);
$idUsuario = $data['id_usuario'] ?? null;

if ($idUsuario) {
    // Instanciar el controlador y procesar la solicitud
    $controller = new GetUsuarioByIdController();
    $response = $controller->getUsuarioById($idUsuario);
    echo $response;
} else {
    // Manejar errores
    echo json_encode([
        'status' => 'error',
        'message' => 'Falta el ID del usuario.'
    ]);
}
