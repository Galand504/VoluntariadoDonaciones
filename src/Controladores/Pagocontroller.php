<?php
require_once 'controllers/PagoController.php';
require_once 'models/Pago.php';
require_once 'config/database.php'; // Configuración de la base de datos

// Crear la conexión a la base de datos
$db = Database::getConnection();

// Instanciar el controlador
$pagoController = new PagoController($db);

// Obtener la URI y el método de solicitud
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Ruta: Crear un nuevo pago
if ($request === '/pagos' && $method === 'POST') {
    $pagoController->crear();
}
// Ruta: Listar todos los pagos
elseif ($request === '/pagos' && $method === 'GET') {
    $pagoController->listar();
}
// Ruta: Actualizar un pago específico
elseif (preg_match('/^\/pagos\/(\d+)$/', $request, $matches) && $method === 'PUT') {
    $idPago = $matches[1];
    $pagoController->actualizar($idPago);
}
// Ruta: Eliminar un pago específico
elseif (preg_match('/^\/pagos\/(\d+)$/', $request, $matches) && $method === 'DELETE') {
    $idPago = $matches[1];
    $pagoController->eliminar($idPago);
}
// Ruta no encontrada
else {
    http_response_code(404);
    echo json_encode(["message" => "Recurso no encontrado."]);
}
?>

