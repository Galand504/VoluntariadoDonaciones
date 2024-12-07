<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Incluir el controlador
use App\Modulos\Usuarios\Controladores\UpdateUsuarioController;
use App\Configuracion\responseHTTP;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener y decodificar los datos
        $inputData = file_get_contents("php://input");
        $data = json_decode($inputData, true);

        // Log para depuración
        error_log("Datos recibidos: " . print_r($data, true));

        // Validar si hay datos
        if (!$data) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se enviaron datos o el formato JSON es inválido.',
                'debug' => json_last_error_msg()
            ]);
            exit;
        }

        // Validar ID de usuario
        if (empty($data['id_usuario'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'El ID de usuario es obligatorio.'
            ]);
            exit;
        }

        // Validar tipo de usuario
        if (empty($data['tipo'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'El tipo de usuario es obligatorio.'
            ]);
            exit;
        }

        // Validar campos específicos según el tipo
        if ($data['tipo'] === 'Persona') {
            if (empty($data['nombre'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'El campo "nombre" es obligatorio para tipo Persona.'
                ]);
                exit;
            }
        } elseif ($data['tipo'] === 'Empresa') {
            if (empty($data['nombreEmpresa'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'El campo "nombreEmpresa" es obligatorio para tipo Empresa.'
                ]);
                exit;
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tipo de usuario no válido.'
            ]);
            exit;
        }

        // Instanciar el controlador y procesar la solicitud
        $controller = new UpdateUsuarioController();
        $response = $controller->updateUsuario($data);

        // Asegurarse de que la respuesta sea un JSON válido
        if (!is_string($response)) {
            $response = json_encode($response);
        }

        echo $response;
    } else {
        echo json_encode(responseHTTP::status400('Método no permitido'));
    }
} catch (Exception $e) {
    error_log("Error en UpdateUsuario.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor',
        'debug' => $e->getMessage()
    ]);
}
