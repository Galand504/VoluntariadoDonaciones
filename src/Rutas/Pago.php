<?php
require_once 'controllers/PagoController.php';
require_once 'models/Pago.php';
require_once 'config/database.php';

$db = Database::getConnection();
$pagoController = new PagoController($db);

// Gestión de rutas
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Manejo de CORS y preflight
if ($method === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    exit(0);
}

// Definición de rutas
if ($request === '/pagos' && $method === 'POST') {
    $pagoController->crear();
} elseif ($request === '/pagos' && $method === 'GET') {
    $pagoController->listar();
} elseif (preg_match('/^\/pagos\/(\d+)$/', $request, $matches) && $method === 'PUT') {
    $pagoController->actualizar($matches[1]);
} elseif (preg_match('/^\/pagos\/(\d+)$/', $request, $matches) && $method === 'DELETE') {
    $pagoController->eliminar($matches[1]);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Recurso no encontrado."]);
}
?>

