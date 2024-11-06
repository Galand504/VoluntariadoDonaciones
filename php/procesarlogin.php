<?php
require_once '../config/db.php'; // Conectar a la base de datos
require_once '../clases/Usuario.php'; // Incluir la clase Usuario

$usuario = new Usuario($db); // Instanciar la clase Usuario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];

    $result = $usuario->login($email, $contraseña);

    if ($result) {
        // Redirigir a la página de inicio o dashboard
        header("Location: ../html/dashboard.html");
        exit();
    } else {
        echo "Correo electrónico o contraseña incorrectos.";
    }
}
?>
