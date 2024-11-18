<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir el controlador
use App\Controladores\UpdateUsuarioController;
use App\Configuracion\responseHTTP;

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

    // Validar los campos necesarios según el tipo
    if (isset($data['Tipo']) && $data['Tipo'] === 'Persona') {
        if (empty($data['nombre'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'El campo "nombre" es obligatorio para tipo Persona.'
            ]);
            exit;
        }
    } elseif (isset($data['Tipo']) && $data['Tipo'] === 'Empresa') {
        if (empty($data['nombreEmpresa'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'El campo "nombreEmpresa" es obligatorio para tipo Empresa.'
            ]);
            exit;
        }
    }

    // Instanciar el controlador y procesar la solicitud
    $controller = new UpdateUsuarioController();
    $response = $controller->updateusuario($data);

    // Retornar la respuesta al cliente
    echo $response;
} else {
    // Método no permitido
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);
}
