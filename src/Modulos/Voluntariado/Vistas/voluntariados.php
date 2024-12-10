<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades de Voluntariado</title>
    <link rel="stylesheet" href="css/voluntariado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="sidebar">
        <div class="logo-details">
            <h1 class="logo-name">Logo</h1>
        </div>
        <ul class="nav-links">
            <li><a href="/src/Modulos/Usuarios/Vistas/menu_usuario.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="../src/Modulos/Recompensas/Vistas/recompensas.php"><i class="fas fa-trophy"></i> Recompensas</a></li>
            <li><a href="#" data-bs-toggle="modal" data-bs-target="#activityModal">
                <i class="fas fa-plus-circle"></i> Registrar Actividad</a></li>
            <li><a href="voluntariados.php"><i class="fas fa-hands-helping"></i> Voluntariados</a></li>
            <li><a href="/src/Modulos/Donaciones/Vistas/donaciones.php"><i class="fas fa-gift"></i> Donaciones</a></li>
        </ul>
    </div>
    <div class="container">
        <!-- Las tarjetas de voluntariado se cargarán aquí dinámicamente -->
    </div>

    <!-- Modal para el formulario de voluntariado -->
    <div id="participarModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Registro de Voluntariado</h2>
            <form id="formularioVoluntariado">
                <input type="hidden" id="id_actividad" name="id_actividad">
                
                <label for="disponibilidad">Disponibilidad:</label>
                <select id="disponibilidad" name="disponibilidad" required>
                    <option value="">Seleccione su disponibilidad</option>
                    <option value="Tiempo Completo">Tiempo Completo</option>
                    <option value="Medio Tiempo">Medio Tiempo</option>
                    <option value="Fines de Semana">Fines de Semana</option>
                    <option value="Por Horas">Por Horas</option>
                </select>

                <button type="submit">Registrarme como Voluntario</button>
            </form>
        </div>
    </div>

    <!-- Agregar esto después del modal de participación -->
    <div id="alertModal" class="modal">
        <div class="modal-content alert-content">
            <span class="close">&times;</span>
            <i class="fas fa-check-circle success-icon"></i>
            <p id="alertMessage"></p>
            <button class="btn-ok">Aceptar</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/voluntariado.js"></script>
</body>
</html> 