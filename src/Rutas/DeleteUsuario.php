<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir el controlador
use App\Controladores\DeleteUsuarioController;

// Verificar que el método sea DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtener el ID del usuario de la URL o del cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    $idUsuario = $data['id_usuario'] ?? null;

    // Validar si se envió el ID del usuario
    if (!$idUsuario) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Falta el ID del usuario a eliminar.'
        ]);
        exit;
    }

    // Instanciar el controlador y procesar la solicitud
    $controller = new DeleteUsuarioController();
    $response = $controller->deleteUsuario($idUsuario);

    // Retornar la respuesta al cliente
    echo $response;
} else {
    // Método no permitido
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);
}
