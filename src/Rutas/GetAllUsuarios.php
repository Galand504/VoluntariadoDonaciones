<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Incluir el controlador
use App\Controladores\GetAllUsuariosController;

// Instanciar el controlador
$controller = new GetAllUsuariosController();
$response = $controller->getAllUsuarios();

// Enviar la respuesta
echo $response;
