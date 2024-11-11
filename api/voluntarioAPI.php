<?php
// Incluir la conexión a la base de datos y la clase Voluntario
include 'db_connection.php$db Voluntario.php';

// Obtener el tipo de solicitud y los datos de entrada
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Configurar la respuesta como JSON
header('Content-Type: application/json');

// Función para enviar respuesta
function sendResponse($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Ejecutar acciones basadas en el método de solicitud
try {
    if ($method === 'POST' && isset($input['action'])) {
        $action = $input['action'];

        // Registrar un nuevo voluntario
        if ($action === 'registrarVoluntario' && isset($input['nombre'], $input['email'])) {
            $id = Voluntario::registrarVoluntario($dbConnection, $input['nombre'], $input['email']);
            if ($id) {
                sendResponse(201, ["message" => "Voluntario registrado", "id" => $id]);
            } else {
                sendResponse(500, ["error" => "Error al registrar el voluntario"]);
            }

        // Cargar un voluntario y ver sus datos
        } elseif ($action === 'cargarVoluntario' && isset($input['id'])) {
            $voluntario = new Voluntario($dbConnection, $input['id']);
            sendResponse(200, $voluntario->obtenerDatos());

        // Ver organizaciones
        } elseif ($action === 'verOrganizaciones') {
            $voluntario = new Voluntario($dbConnection); // Puedes pasar un ID si necesitas cargar a un voluntario específico
            sendResponse(200, $voluntario->verOrganizaciones());

        // Donar a una organización
        } elseif ($action === 'donar' && isset($input['id'], $input['organizacionId'], $input['monto'], $input['tipo'])) {
            $voluntario = new Voluntario($dbConnection, $input['id']);
            if ($voluntario->donar($input['organizacionId'], $input['monto'], $input['tipo'])) {
                sendResponse(200, ["message" => "Donación realizada con éxito"]);
            } else {
                sendResponse(500, ["error" => "Error al realizar la donación"]);
            }

        // Obtener historial de donaciones
        } elseif ($action === 'obtenerDonaciones' && isset($input['id'])) {
            $voluntario = new Voluntario($dbConnection, $input['id']);
            sendResponse(200, $voluntario->obtenerDonaciones());

        } else {
            sendResponse(400, ["error" => "Acción o parámetros no válidos"]);
        }

    } else {
        sendResponse(405, ["error" => "Método no permitido"]);
    }
} catch (Exception $e) {
    sendResponse(500, ["error" => $e->getMessage()]);
}

?>

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

