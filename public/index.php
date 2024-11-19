<?php
use App\configuracion\errorlogs;
use App\configuracion\responseHTTP;
require dirname(__DIR__) . '/vendor/autoload.php';
errorlogs::activa_error_logs();
if (isset($_GET['route'])) {
    
    $url = explode('/', $_GET['route']);

<<<<<<< HEAD
    $lista = ['auth', 'user', 'AddUsuario', 'UpdateUsuario', 'DeleteUsuario', 'GetAllUsuarios', 'GetUsuarioById', 'pruebabase']; // lista de rutas permitidas
=======
    $lista = ['auth', 'user', 'AddUsuario', 'UpdateUsuario', 'DeleteUsuario', 'GetAllUsuarios', 'GetUsuarioById', 'DonacionAPI']; // lista de rutas permitidas
>>>>>>> f467e625a3228c2a7d85ff61d993aa0dfb69d184

    $file = dirname(__DIR__) . '/src/rutas/' . $url[0] . '.php';

    if (!in_array($url[0], $lista)) {
        // echo "La ruta no existe";
    echo json_encode(responseHTTP::status400());
    error_log("Esto es una prueba de un error");
        //header("HTTP/1.1 404 Not Found");
        exit; // Finalizamos la ejecución si la ruta no es válida
    }

    if(!file_exists($file) || !is_readable($file)){
        echo "El archivo no existe o no es legible";
    }else{
        require $file;
        exit;
    }

} else {
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <title>Página Principal</title>
    
 </head>
<body>
    <header>
    <div class="logo-container">
    <a href="index.php">
    <img src="../img/Logo.jpg" alt="Logotipo" class="logo"> 
    </a>
</div>
        <h1>Bienvenido a Manos Solidarias</h1>
        <div class="auth-buttons">
            <a href="../html/registrar.html" class="btn">Registrarse</a>
            <a href="../html/login.html" class="btn">Iniciar Sesión</a>
        </div>
    </header>
<div class="slider-box">
    <ul>
        <li>
            <img src="../img/image1.jpeg" alt="Imagen 1">
        </li>
        <li>
    <a href="#SobreNosotros">
            <img src="../img/image2.jpeg" alt="Imagen 2">
    </a>
        </li>
        <li>
            <img src="../img/image3.jpeg" alt="Imagen 3">
        </li>
        <li>    
            <img src="../img/image4.jpeg" alt="Imagen 4">
        </li>       
    </ul>
    </div>
    <main>
        <h2>Descripción Breve</h2>
        <p>Nuestra plataforma permite a los usuarios hacer donaciones y participar en campañas de voluntariado de manera sencilla y segura.</p>
        <h2>Características Principales</h2>
        <ul>
            <li>Registro y autenticación de usuarios.</li>
            <li>Creación y gestión de campañas.</li>
            <li>Pasarela de pago integrada.</li>
            <li>Sistema de notificaciones.</li>
            <li>Reportes de donaciones.</li>
        </ul>
    </main>
    <main> 
    <section id="#SobreNosotros">
        <h2>Sobre Nosotros</h2>
    </section>
    </main>

    <footer>
        <p>&copy; 2024 Plataforma de Donaciones y Voluntariado. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
