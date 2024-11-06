<?php
require_once '../config/db.php'; // Conectar a la base de datos
require_once '../clases/Usuario.php'; // Incluir la clase Usuario

$usuario = new Usuario($db); // Instanciar la clase Usuario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];

    $result = $usuario->registrar($nombre, $email, $contraseña);

    if ($result) {
        // Redirigir a la página de inicio o mostrar un mensaje de éxito
        header("Location: ../html/login.html");
        exit();
    } else {
        echo "Error al registrar el usuario.";
    }
}
?>
