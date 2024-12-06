<?php

use App\Modulos\Recompensas\Controladores\recompensaController;

$data = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['usuario_id'])) {
            recompensaController::obtenerRecompensasPorUsuario($_GET['usuario_id']);
        } else {
            recompensaController::obtenerDonadoresEstrella();
        }
        break;

    case 'POST':
        recompensaController::registrarRecompensa($data);
        break;

    case 'PUT':
        if (isset($data['usuario_id'])) {
            recompensaController::verificarElegibilidad($data['usuario_id']);
        } else {
            echo json_encode(['error' => 'Usuario no especificado']);
        }
        break;

    default:
        echo json_encode(['error' => 'Método no permitido']);
}
