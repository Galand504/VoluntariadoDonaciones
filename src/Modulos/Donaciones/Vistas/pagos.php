<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/pagos.css">
</head>
<body>
<div class="sidebar">
        <ul class="nav-links">
            <li><a href="/src/Modulos/Usuarios/Vistas/Admin.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="/src/Modulos/Usuarios/Vistas/crud.php"><i class="fas fa-database"></i> Gestor de usuarios</a></li>
            <li><a href="/src/Modulos/Proyectos/Vistas/proyectos.php"><i class="fas fa-project-diagram"></i> Gestor de Proyectos</a></li>
            <li><a href="/src/Modulos/Recompensas/Vistas/recompensas.php"><i class="fas fa-trophy"></i> Gestor de Recompensas</a></li>
            <li><a href="/src/Modulos/Donaciones/Vistas/pagos.php"><i class="fas fa-credit-card"></i> Gestor de Pagos</a></li>
        </ul>
    </div>
    <div class="container-fluid mt-4">
        <h2>Gestión de Pagos</h2>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Filtros</h5>
            </div>
            <div class="card-body">
                <form id="filtrosPagos" class="mb-4">
                    <div class="row g-3">
                        <!-- Filtros por fecha -->
                        <div class="col-md-3">
                            <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fechaInicio">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="filtrarPorFecha">
                                <i class="fas fa-calendar"></i> Filtrar
                            </button>
                        </div>

                        <!-- Filtro por usuario -->
                        <div class="col-md-3">
                            <label for="idUsuario" class="form-label">ID Usuario</label>
                            <input type="text" class="form-control" id="idUsuario">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="filtrarPorUsuario">
                                <i class="fas fa-user"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Total Pagos</h6>
                        <h4 id="totalPagos">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Completados</h6>
                        <h4 id="pagosCompletados">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Pendientes</h6>
                        <h4 id="pagosPendientes">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Cancelados</h6>
                        <h4 id="pagosCancelados">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Monto Total</h6>
                        <h4 id="montoTotal">L. 0.00</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Pagos -->
        <div class="table-responsive">
            <table id="tablaPagos" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Pago</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Moneda</th>
                        <th>Estado</th>
                        <th>ID Donación</th>
                        <th>ID Usuario</th>
                        <th>Método de Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detalles Pago -->
    <div class="modal fade" id="modalDetallesPago" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID Pago:</strong> <span id="detallePagoId"></span></p>
                            <p><strong>Fecha:</strong> <span id="detalleFecha"></span></p>
                            <p><strong>Monto:</strong> <span id="detalleMonto"></span></p>
                            <p><strong>Moneda:</strong> <span id="detalleMoneda"></span></p>
                            <p><strong>Estado:</strong> <span id="detalleEstado"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Proyecto:</strong> <span id="detalleProyecto"></span></p>
                            <p><strong>Donante:</strong> <span id="detalleDonante"></span></p>
                            <p><strong>Método de Pago:</strong> <span id="detalleMetodoPago"></span></p>
                            <p><strong>Referencia Externa:</strong> <span id="detalleReferencia"></span></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Actualizar Estado</h6>
                        <select class="form-select" id="actualizarEstado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Completado">Completado</option>
                            <option value="Fallido">Fallido</option>
                        </select>
                        <button class="btn btn-primary mt-2" onclick="actualizarEstadoPago()">Guardar Estado</button>
                    </div>
                    <div class="mt-3">
                        <h6>Cancelar Pago</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" id="motivoCancelacion" placeholder="Motivo de cancelación">
                            <button class="btn btn-danger" onclick="cancelarPago()">Cancelar Pago</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template para el modal de detalles (agregar esto donde prefieras, puede ser al final antes de los scripts) -->
    <template id="detallesPagoTemplate">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th class="w-30">ID Pago</th>
                    <td data-detalle="idPago"></td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td data-detalle="fecha"></td>
                </tr>
                <tr>
                    <th>Monto</th>
                    <td data-detalle="monto"></td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td data-detalle="estado"></td>
                </tr>
                <tr>
                    <th>Referencia</th>
                    <td data-detalle="referencia"></td>
                </tr>
                <tr>
                    <th>Donante</th>
                    <td data-detalle="donante"></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td data-detalle="email"></td>
                </tr>
                <tr>
                    <th>Proyecto</th>
                    <td data-detalle="proyecto"></td>
                </tr>
                <tr>
                    <th>Método de Pago</th>
                    <td data-detalle="metodoPago"></td>
                </tr>
            </table>
        </div>
    </template>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/pagos.js"></script>
</body>
</html>
