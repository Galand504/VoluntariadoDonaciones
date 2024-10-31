<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">
        <h1>Bienvenido a Manos Solidarias</h1>
        <div class="auth-buttons">
            <a href="html/registrar.html" class="btn">Registrarse</a>
            <a href="html/login.html" class="btn">Iniciar Sesión</a>
        </div>
    </header>
<div class="slider-container">
    <div class="slider" id="carouselImages">
            <img src="../recgraficos/image1.jpg" alt="Imagen 1">
            <img src="../recgraficos/image2.jpg" alt="Imagen 2">
            <img src="../recgraficos/image3.jpg" alt="Imagen 3">
            <img src="../recgraficos/image4.jpg" alt="Imagen 4">
        </div>
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

    <footer>
        <p>&copy; 2024 Plataforma de Donaciones y Voluntariado. Todos los derechos reservados.</p>
    </footer>
    <script>
        const images = [
            "../recgraficos/image1.jpg",
            "../recgraficos/image2.jpg",
            "../recgraficos/image3.jpg",
            "../recgraficos/image4.jpg"
        ];

        const carousel = document.getElementById('carouselImages');

        // Duplicar imágenes
        images.forEach(image => {
            const imgElement = document.createElement('img');
            imgElement.src = image;
            imgElement.alt = `Imagen ${images.indexOf(image) + 1}`;
            carousel.appendChild(imgElement); // Agregar cada imagen al slider
        });

        let currentIndex = 0;
        const totalImages = images.length; // Número de imágenes originales

        function moveCarousel(direction) {
            currentIndex = (currentIndex + direction + totalImages) % totalImages; // Ajuste para las imágenes originales
            carousel.style.transform = `translateX(${-currentIndex * (100 / (totalImages * 2))}%)`;
        }

        // Iniciar el carrusel automáticamente
        setInterval(() => {
            moveCarousel(1);
        }, 2000);
    </script>
</body>
</html>