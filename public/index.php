<?php
use App\configuracion\errorlogs;
use App\configuracion\responseHTTP;
require dirname(__DIR__) . '/vendor/autoload.php';
errorlogs::activa_error_logs();
if (isset($_GET['route'])) {
    
    $url = explode('/', trim($_GET['route'], '/'));
    
    if (empty($url[0])) {
        echo json_encode(responseHTTP::status400());
        exit;
    }

    // Detectar automáticamente los módulos disponibles
    $modulosPath = dirname(__DIR__) . '/src/modulos';
    $modulos = array_filter(scandir($modulosPath), function($item) use ($modulosPath) {
        return is_dir($modulosPath . '/' . $item) && !in_array($item, ['.', '..']);
    });

    // Buscar el archivo de ruta en cada módulo
    $found = false;
    foreach ($modulos as $modulo) {
        $file = $modulosPath . '/' . $modulo . '/rutas/' . $url[0] . '.php';
        if (file_exists($file) && is_readable($file)) {
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo json_encode(responseHTTP::status404("Ruta no encontrada"));
        exit;
    }

    require $file;
    exit;

} else {
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Incluir Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <title>Página Principal</title>
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo-container">
                <a href="index.php">
                    <img src="../img/Logo.jpg" alt="Logotipo" class="logo"> 
                </a>
            </div>
            <h1 class="h2">Bienvenido a Manos Solidarias</h1>
            <div class="auth-buttons">
                <a href="../html/registrar.html" class="btn btn-outline-light me-2">Registrarse</a>
                <a href="../html/login.html" class="btn btn-light">Iniciar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Slider con Bootstrap (Carousel Automático) -->
    <div id="carouselExampleAutoplay" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../img/image1.jpeg" class="d-block w-100" alt="Imagen 1">
            </div>
            <div class="carousel-item">
                <a href="#SobreNosotros">
                    <img src="../img/image2.jpeg" class="d-block w-100" alt="Imagen 2">
                </a>
            </div>
            <div class="carousel-item">
                <img src="../img/image3.jpeg" class="d-block w-100" alt="Imagen 3">
            </div>
            <div class="carousel-item">
                <img src="../img/image4.jpeg" class="d-block w-100" alt="Imagen 4">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplay" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplay" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <main class="container mt-5">
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
    
    <section id="SobreNosotros" class="container mt-5">
        <h2>Sobre Nosotros</h2>
        <p>Aquí va información sobre la empresa o la plataforma...</p>
    </section>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2024 Plataforma de Donaciones y Voluntariado. Todos los derechos reservados.</p>
    </footer>

    <!-- Incluir Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
