<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir el controlador
use App\Modulos\Usuarios\Controladores\DeleteUsuarioController;

// Verificar que el método sea DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtener datos del body
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Intentar obtener el ID del body primero
    $idUsuario = isset($data['id_usuario']) ? $data['id_usuario'] : null;
    
    // Si no hay ID en el body, intentar obtenerlo de la URL
    if (!$idUsuario) {
        $url_components = parse_url($_SERVER['REQUEST_URI']);
        $path = explode('/', trim($url_components['path'], '/'));
        
        for ($i = 0; $i < count($path); $i++) {
            if ($path[$i] === 'DeleteUsuario' && isset($path[$i + 1])) {
                $idUsuario = $path[$i + 1];
                break;
            }
        }
    }

    // Validar si se envió el ID del usuario
    if (!$idUsuario || !is_numeric($idUsuario)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de usuario inválido o no proporcionado'
        ]);
        exit;
    }

    // Instanciar el controlador y procesar la solicitud
    $controller = new DeleteUsuarioController();
    $response = $controller->deleteUsuario($idUsuario);

    // Retornar la respuesta al cliente
    echo json_encode($response);
} else {
    // Método no permitido
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);
}

