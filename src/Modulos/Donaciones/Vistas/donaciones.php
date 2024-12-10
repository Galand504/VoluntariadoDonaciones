<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades de Recaudación de Fondos</title>
    <link rel="stylesheet" href="/css/donaciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>

    <div class="container">
        <!-- Ejemplo de invitación de recaudación de fondos -->
        <div class="invitacion">
            <i class="fas fa-donate icono"></i>
            <div class="detalle">
                <p class="titulo">Recaudación para Equipos Médicos</p>
                <p class="descripcion">Ayuda a financiar equipos médicos esenciales para el hospital comunitario.</p>
                <p class="meta-recaudacion"><strong>Meta:</strong> $10,000 &nbsp;|&nbsp; <strong>Recaudado:</strong> $5,000</p>
                <div class="progreso-recaudacion">
                    <div class="progreso-barra" style="width: 50%;"></div>
                </div>
                <button class="boton-participar" id="donarBtn">Donar Ahora</button>
            </div>
        </div>
    </div>

    <!-- Modal para el formulario de donación -->
    <div id="donarModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Formulario de Donación</h2>
            <form action="#">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="monto">Monto a donar ($):</label>
                <input type="number" id="monto" name="monto" required>

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>

                <button type="submit">Donar</button>
            </form>
        </div>
    </div>

    <script src="/js/donaciones.js"></script>
</body>
</html>
