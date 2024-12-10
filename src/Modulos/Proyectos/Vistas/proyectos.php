<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proyectos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/proyecto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="sidebar">
        <ul class="nav-links">
            <li><a href="/src/Modulos/Usuarios/Vistas/Admin.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="/src/Modulos/Usuarios/Vistas/crud.php"><i class="fas fa-database"></i> Gestor de usuarios</a></li>
            <li><a href="proyectos.php"><i class="fas fa-project-diagram"></i> Gestor de Proyectos</a></li>
            <li><a href="/src/Modulos/Recompensas/Vistas/recompensas.php"><i class="fas fa-trophy"></i> Gestor de Recompensas</a></li>
            <li><a href="/src/Modulos/Donaciones/Vistas/pagos.php"><i class="fas fa-credit-card"></i> Gestor de Pagos</a></li>
        </ul>
    </div>
    <div class="container mt-4">
        <h1 class="text-center">Gestión de Proyectos</h1>
        <hr>

        <form id="proyectoForm">
            <input type="hidden" id="idProyecto" name="idProyecto">
            
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="objetivo" class="form-label">Objetivo</label>
                <input type="text" class="form-control" id="objetivo" name="objetivo" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="meta" class="form-label">Meta</label>
                        <input type="number" 
                               step="0.01" 
                               class="form-control" 
                               id="meta" 
                               name="meta" 
                               min="0" 
                               value="0">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="moneda" class="form-label">Moneda</label>
                        <select class="form-select" id="moneda" name="moneda" required>
                            <option value="HNL" selected>Lempiras (HNL)</option>
                            <option value="USD">Dólares (USD)</option>
                            <option value="EUR">Euros (EUR)</option>
                            <option value="MXN">Pesos Mexicanos (MXN)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado" required>
                    <option value="En Proceso">En Proceso</option>
                    <option value="Completado">Completado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="tipo_actividad" class="form-label">Tipo de Actividad</label>
                <select class="form-select" id="tipo_actividad" name="tipo_actividad" required>
                    <option value="Voluntariado">Voluntariado</option>
                    <option value="Donacion">Donación</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
                <button type="button" class="btn btn-secondary" style="display: none;" 
                        onclick="cancelarEdicion()">Cancelar</button>
            </div>
        </form>

        <hr>
        <h2 class="mt-4">Lista de Proyectos</h2>
        <table class="table table-striped" id="proyectosTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Objetivo</th>
                    <th>Meta</th>
                    <th>Estado</th>
                    <th>Tipo de Actividad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script src="js/proyectos.js"></script>
</body>
</html>