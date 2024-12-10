<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recompensas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/recompensas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="sidebar">
        <ul class="nav-links">
            <li><a href="/src/Modulos/Usuarios/Vistas/Admin.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="/src/Modulos/Usuarios/Vistas/crud.php"><i class="fas fa-database"></i> Gestor de usuarios</a></li>
            <li><a href="/src/Modulos/Proyectos/Vistas/proyectos.php"><i class="fas fa-project-diagram"></i> Gestor de Proyectos</a></li>
            <li><a href="recompensas.php"><i class="fas fa-trophy"></i> Gestor de Recompensas</a></li>
            <li><a href="/src/Modulos/Donaciones/Vistas/pagos.php"><i class="fas fa-credit-card"></i> Gestor de Pagos</a></li>
        </ul>
    </div>
    <div class="container mt-4">
        <h1 class="text-center">Gestión de Recompensas</h1>
        <hr>

        <form id="recompensaForm">
            <input type="hidden" id="idRecompensa" name="idRecompensa">
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="montoMinimo" class="form-label">Monto Mínimo</label>
                <input type="number" class="form-control" id="montoMinimo" name="montoMinimo" step="0.01" min="0" required>
            </div>

            <div class="mb-3">
                <label for="moneda" class="form-label">Moneda</label>
                <select class="form-select" id="moneda" name="moneda" required>
                    <option value="HNL" selected>Lempiras (HNL)</option>
                    <option value="USD">Dólares (USD)</option>
                    <option value="EUR">Euros (EUR)</option>
                    <option value="MXN">Pesos Mexicanos (MXN)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="fechaEntregaEstimada" class="form-label">Fecha de Entrega Estimada</label>
                <input type="date" class="form-control" id="fechaEntregaEstimada" name="fechaEntregaEstimada" required>
            </div>

            <div class="mb-3">
                <label for="idProyecto" class="form-label">Proyecto</label>
                <select class="form-select" id="idProyecto" name="idProyecto" required>
                    <!-- Opciones de proyectos -->
                </select>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Aprobada">Aprobada</option>
                    <option value="Rechazada">Rechazada</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar Recompensa</button>
                <button type="button" class="btn btn-secondary" style="display: none;" 
                        onclick="cancelarEdicion()">Cancelar</button>
            </div>
        </form>

        <hr>
        <h2 class="mt-4">Lista de Recompensas</h2>
        <table class="table table-striped" id="recompensasTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Monto Mínimo</th>
                    <th>Moneda</th>
                    <th>Fecha de Entrega Estimada</th>
                    <th>Proyecto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script src="js/recompensas.js"></script>
</body>
</html>
