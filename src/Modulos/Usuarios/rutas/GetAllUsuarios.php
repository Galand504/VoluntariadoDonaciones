<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Incluir el controlador
use App\Modulos\Usuarios\Controladores\GetAllUsuariosController;

// Instanciar el controlador
$controller = new GetAllUsuariosController();
$response = $controller->getAllUsuarios();

// Enviar la respuesta
echo json_encode($response);
